<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Traits\TransactionTrait;
use Str;

class BotTelegramController extends Controller
{
    use TransactionTrait;

    public function setWebhook()
    {
        Telegram::setWebhook(['url' => config('telegram.bots.mybot.webhook_url')]);
    }

    public function getWebhookBot()
    {
        $webhook =  Telegram::commandsHandler(true);

        $command = $webhook->getChat();
        $getText = $webhook->getMessage()->getText();
        $chatId = $command->getId();
        $username = $command->getUsername();
        $getCommand = explode(' ', $getText);
        $success = '\u2705';
        $failed = '\u274c';

        $user = User::where('username', $username)->first();
        if(!$user){
            $this->sendMessage($chatId, $this->unicodeToUtf8($failed,' User tidak terdaftar'));
        }else{
            $this->authenticate($chatId, $getCommand, $getText);
        }
    }

    public function authenticate($chatId, $getCommand, $text)
    {
        $success = '\u2705';
        $failed = '\u274c';

        switch ($getCommand[0]) {
            case '/start':
                $this->sendMessage($chatId, 'Selamat Datang di E-Dompet Bot. klik /help untuk melihat command akuu.');
                break;
            case '/help':
                $commands = [
                    'listkategori' => 'Daftar Kategori',
                    'listdompet' => 'Daftar Dompet',
                    'kategori' => 'Command membuat kategori baru',
                    'transaksi' => 'Command membuat transaksi baru',
                    'laporan' => 'Command untuk melihat transaksi',
                    'reload' => 'Jika Ada Kendala, pakai aku yaa!'
                ];
                $messages = '';
                foreach ($commands as $key => $value) {
                    $messages .= $this->formatText('/%s - %s' . PHP_EOL, $key, $value);
                }
                $this->sendMessage($chatId, $messages);
                break;
            case '/listkategori':
                $categories = Category::all();
                $messages = '';
                foreach ($categories as $value) {
                    $messages .= $this->formatText('- %s' . PHP_EOL, $value->name);
                }
                $this->sendMessage($chatId, $messages);
                break;
            case '/listdompet':
                $wallets = Wallet::orderByDesc('balance')->get();
                $messages = '';
                foreach ($wallets as $value) {

                    $messages .= $this->formatText('- %s  =  Rp. %s ' . PHP_EOL, $value->name, number_format($value->balance, 0, ".", '.'));
                }
                $this->sendMessage($chatId, $messages);
                break;
            case '/kategori':
                if (empty($getCommand[1])) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Masukan nama kategori ( /kategori #nama #kode_warna )'));
                    break;
                }
                $replyMessage = explode('#', $text);

                $category = Category::where('name', $replyMessage[1])->first();
                if ($category) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Kategori sudah digunakan'));
                    break;
                }

                try {
                    Category::create([
                        'name' => Str::slug($replyMessage[1], ' '),
                        'color' => Str::slug($replyMessage[2] ?? null,' ') ,
                    ]);
                    $this->sendMessage($chatId, $this->unicodeToUtf8($success, ' Kategori berhasil dibuat'));
                } catch (Exception $e) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, $e->getMessage()));
                }
                break;
            case '/transaksi':
                $replyMessage = explode('#', $text);

                if (empty($replyMessage[1])) {
                    $this->sendMessage($chatId, 'Format Transaksi salah. /transaksi #dompet #kategori #uang #tipe_transaksi #catatan');
                    break;
                }

                $wallet = $this->getWallet($replyMessage[1]);

                if (!$wallet) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Dompet tidak terdaftar'));
                    break;
                }

                $category = $this->getCategory($replyMessage[2]);

                if (!$category) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Kategori tidak ada gaes'));
                    break;
                }

                DB::beginTransaction();
                try {
                    $transaction = Transaction::create([
                        'wallet_id' => $wallet->id,
                        'amount' => Str::slug($replyMessage[3], ' '),
                        'type' => Str::slug($replyMessage[4], ' '),
                        'note' => Str::slug($replyMessage[5], ' '),
                    ]);

                    $transaction->categories()->attach($category->id);
                    $this->updateWalletBalance($transaction->wallet, $transaction->amount, $transaction->type, $transaction->note);
                    DB::commit();

                    $this->sendMessage($chatId, $this->unicodeToUtf8($success, 'Transaksi berhasil dibuat'));
                } catch (Exception $e) {
                    DB::rollBack();
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, $e->getMessage()));
                }
                break;
            case '/laporan':
                    $replyMessage = explode('#', $text);
                    if (empty($replyMessage[1])) {
                        $this->sendMessage($chatId, 'Format Laporan salah. /laporan #dompet #tanggal(optional Y-m-d) #tipe_transaksi(default:pengeluaran)');
                        break;
                    }

                    $date = $replyMessage[2] ?? date('Y-m-d');
                    $type = $replyMessage[3] ?? 'pengeluaran';

                    // $response = "<b> Laporan Transaksi Dompet {$replyMessage[1]} Tanggal {$date} ( {$type} )</b>";

                    $wallet = $this->getWallet($replyMessage[1]);

                    if (!$wallet) {
                        $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Dompet tidak terdaftar'));
                        break;
                    }

                    $transactions = $this->getTransaction($wallet->id, $date, $type);
                    $response = $this->formatText('%s'.PHP_EOL,"<b> Laporan Transaksi Dompet {$wallet->name} Tanggal {$date} ( {$type} ) </b>");

                    if(count($transactions) > 0 ){
                        foreach ($transactions as $key => $value) {
                            $no = $key+1;
                            $response .= $this->formatText("$no. Rp %s ( %s )" . PHP_EOL, number_format($value->amount, 0, ".", '.'),$value->note);
                        }
                        $this->sendMessage($chatId,  $response);
                        break;
                    }

                    $response .= '<b> Tidak Ditemukan </b>';
                    $this->sendMessage($chatId,  $this->unicodeToUtf8($failed,$response));
                    break;
            case '/reload':
                $this->setWebhook();
                $this->sendMessage($chatId,  $this->unicodeToUtf8($success, ' reload success'));

                break;
            default:
                $this->sendMessage($chatId,  $this->unicodeToUtf8($failed, ' Ups Command tidak ada'));
                break;
        }
    }

    public function sendMessage($chatId, $message)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'html'
        ]);
    }

    public function unicodeToUtf8($string, $message)
    {
        $icon = html_entity_decode(
            preg_replace(
                "/U\+([0-9A-F]{4})/",
                "&#x\\1;",
                $string
            ),
            ENT_NOQUOTES,
            'UTF-8'
        );

        return json_decode(('"' . $icon . $message . '"'));
    }

    public function formatText($format, $message1, $message2 = null)
    {
        return sprintf($format, $message1, $message2);
    }

    public function getWallet($name)
    {
        return Wallet::where('name', Str::slug($name,' '))->first();
    }

    public function getCategory($name)
    {
        return Category::where('name', Str::slug($name,' '))->first();
    }

    public function getTransaction($wallet_id, $date, $tipe)
    {
        return Transaction::where('wallet_id', $wallet_id)->whereDate('created_at', $date)->where('type', $tipe)->get();
    }
}
