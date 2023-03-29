<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Traits\TransactionTrait;
use Carbon\Carbon;
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
        $username = $webhook->getMessage()->from->username;
        $getCommand = explode(' ', $getText);
        $failed = '\u274c';

        $user = User::where('username', $username)->first();
        if(!$user){
            $this->sendMessage($chatId, $this->unicodeToUtf8($failed,' User tidak terdaftar'));
        }else{
            $this->authenticate($chatId, $getCommand, $getText, $username);
        }
    }

    public function authenticate($chatId, $getCommand, $text, $command)
    {
        $success = '\u2705';
        $failed = '\u274c';

        switch ($getCommand[0]) {
            case '/start':
                $this->sendMessage($chatId, 'Selamat Datang di E-Dompet Bot. klik /help untuk melihat command akuu.');
                break;
            case '/help':
                $commands = [
                    'listkategori' => 'Command Menampilkan Daftar Kategori',
                    'listdompet' => 'Command Menampilkan Daftar Dompet',
                    'ceksaldo' => 'Command Menampilkan Cek Saldo Dompet',
                    'kategori' => 'Command membuat kategori baru',
                    'transaksi' => 'Command membuat transaksi baru',
                    'laporan' => 'Command untuk melihat laporan transaksi',
                    'transfer' => 'Command untuk melakukan transfer antar dompet',
                    'reload' => 'Jika Ada Kendala, pakai aku yaa!'
                ];

                if(!empty($getCommand[1])){
                    $response = $this->formatText('%s'.PHP_EOL,'<b>Cara menggunakan command : </b>');
                    switch (trim($getCommand[1])) {

                        case 'ceksaldo':
                            $response .= $this->formatText('%s'.PHP_EOL,'<b>/ceksaldo # nama dompet (ex: Uang Dapur)</b>');
                            $this->sendMessage($chatId, $response);
                            break;
                        case 'kategori':
                            $response .= $this->formatText('%s'.PHP_EOL,'<b>/kategori # masukan nama kategori # kode warna (opsional)</b>');
                            $this->sendMessage($chatId, $response);
                            break;
                        case 'transaksi':
                            $response .= $this->formatText('%s'.PHP_EOL,'<b>/transaksi # nama dompet # nama kategori # jumlah uang dikeluarkan # tipe transaksi (pengeluaran/pemasukan) # note </b>');
                            $this->sendMessage($chatId, $response);
                            break;
                        case 'laporan':
                            $response .= $this->formatText('%s' . PHP_EOL, '<b>/laporan # nama dompet # tanggal (all untuk semua tanggal) # tipe transaksi (pengeluaran/pemasukan) </b>');
                            $this->sendMessage($chatId, $response);
                            break;
                        case 'laporan':
                            $response .= $this->formatText('%s' . PHP_EOL, '<b>/transfer # nama dompet asal # nama dompet tujuan # jumlah uang # note </b>');
                            $this->sendMessage($chatId, $response);
                            break;
                        default:
                            $this->sendMessage($chatId, '<b>help command tidak ditemukan </b>');
                            break;
                        break;
                    }
                }else{
                    $messages = '';
                    foreach ($commands as $key => $value) {
                        $messages .= $this->formatText('/%s - %s' . PHP_EOL, $key, $value);
                    }
                    $this->sendMessage($chatId, $messages);
                }
                break;
            case '/listkategori':
                $categories = Category::all();
                $messages = $this->formatText('<b> %s </b>'.PHP_EOL, 'List Katergori : ');
                foreach ($categories as $value) {
                    $messages .= $this->formatText('- %s' . PHP_EOL, $value->name);
                }
                $this->sendMessage($chatId, $messages);
                break;
            case '/listdompet':
                $wallets = Wallet::orderByDesc('balance')->get();
                $messages = $this->formatText('<b> %s </b>'.PHP_EOL, 'List Dompet : ');
                foreach ($wallets as $value) {

                    $messages .= $this->formatText('- %s' . PHP_EOL, $value->name);
                }
                $this->sendMessage($chatId, $messages);
                break;
            case '/kategori':
                if (empty($getCommand[1])) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Masukan nama kategori ( /kategori #nama #kode_warna )'));
                    break;
                }
                $replyMessage = explode('#', $text);

                $category = Category::where('name', trim($replyMessage[1]))->first();
                if ($category) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Kategori sudah digunakan'));
                    break;
                }

                try {
                    Category::create([
                        'name' => $this->whiteSpaces($replyMessage[1]),
                        'color' => $replyMessage[2] ?? null ,
                    ]);
                    $this->sendMessage($chatId, $this->unicodeToUtf8($success, ' Kategori berhasil dibuat'));
                } catch (Exception $e) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, $e->getMessage()));
                }
                break;
            case '/transaksi':
                $replyMessage = explode('#', $text);

                if (empty($replyMessage[1])) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed,' Format Transaksi salah. /transaksi #dompet #kategori #uang #tipe_transaksi #catatan'));
                    break;
                }

                $wallet = $this->getWallet(array(trim($replyMessage[1])));

                if (count($wallet) == 0) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Dompet tidak terdaftar'));
                    break;
                }

                $category = $this->getCategory(trim($replyMessage[2]));
                if (!$category) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Kategori tidak ada gaes'));
                    break;
                }

                DB::beginTransaction();
                try {
                    $transaction = Transaction::create([
                        'wallet_id' => $wallet[0]->id,
                        'amount' => trim($replyMessage[3], ' '),
                        'type' => trim($replyMessage[4], ' '),
                        'note' => trim($replyMessage[5], ' '),
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

                    $wallet_names = !empty($replyMessage[1]) && trim($replyMessage[1]) != 'all' ? array(trim($replyMessage[1])) : Wallet::pluck('name')->toArray();
                    $date = $replyMessage[2] ?? date('Y-m-d');
                    $type = $replyMessage[3] ?? 'pengeluaran';

                    $wallets = $this->getWallet($wallet_names);

                    if (count($wallets) == 0) {
                        $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Dompet tidak terdaftar'));
                        break;
                    }

                try {
                    $transactions = $this->getTransaction($wallets->pluck('id')->toArray(), $date, $type);
                    $start_date = Carbon::parse($transactions->get()[0]?->created_at)->format('d M Y');
                    $end_date = Carbon::parse($transactions->latest()->get()[0]?->created_at)->format('d M Y');
                    $wallet_name = count($wallets) > 1 ? 'All' : $wallets[0]->name;
                    $response = $this->formatText('%s' . PHP_EOL, "<b> Laporan Transaksi {$wallet_name} Tanggal {$start_date} - {$end_date} ( {$type} ) </b>");
                    $response .= $this->formatText('%s'.PHP_EOL,'');
                    if (count($transactions->get()) > 0) {
                        $transactions->chunk(100, function ($transaction) use ($response, $wallet_name, $chatId, $type) {
                            foreach ($transaction as $key => $value) {
                                $no = $key + 1;
                                $response .= $this->formatText("$no. Rp %s ( %s )" . PHP_EOL, number_format($value->amount, 0, ".", '.'), $wallet_name != 'all' ? $value->note : $value->wallet?->name);
                            }
                            $response .= $this->formatText('%s' . PHP_EOL, '');
                            $response .= $this->formatText('<b>%s : Rp. %s </b>' . PHP_EOL, "Total {$type}", number_format($transaction->sum('amount'), 0, '.', '.'));
                            $this->sendMessage($chatId,  $response);
                        });
                        break;
                    }

                    $response .= $this->formatText('%s'.PHP_EOL,'<b> Tidak Ditemukan </b>');
                    $this->sendMessage($chatId,  $response);
                    break;
                } catch (\Exception $e) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, $e->getMessage()));
                }
                break;
            case '/ceksaldo':
                $replyMessage = explode('#', $text);

                if (empty($replyMessage[1])) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Format Cek Saldo salah. /ceksaldo #nama_dompet'));
                    break;
                }

                $wallet = $this->getWallet(array(trim($replyMessage[1])));

                if (count($wallet) == 0) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Dompet tidak terdaftar'));
                    break;
                }

                $saldo = number_format($wallet[0]->balance,0,'.','.');
                $message = $this->unicodeToUtf8($success,"Saldo Dompet {$wallet[0]->name} Rp. {$saldo}");
                $this->sendMessage($chatId, $message);
                break;
            case '/reload':
                $this->setWebhook();
                $this->sendMessage($chatId,  $this->unicodeToUtf8($success, ' reload success'));
                break;
            case '/transfer':
                $replyMessage = explode('#', $text);

                if (empty($replyMessage[1])) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Format Transfer salah. /transfer #dompet asal #dompet tujuan #jumlah uang #catatan'));
                    break;
                }

                if (empty($replyMessage[3])) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Masukan Jumlah Uang'));
                    break;
                }

                $walletFrom = $this->getWallet(array(trim($replyMessage[1])));


                if (count($walletFrom) == 0) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Dompet asal tidak terdaftar'));
                    break;
                }

                $walletTo = $this->getWallet(array(trim($replyMessage[2])));

                if (count($walletTo) == 0) {
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, ' Dompet tujuan tidak terdaftar'));
                    break;
                }

                DB::beginTransaction();
                try {
                    $transfer = Transfer::create([
                        'from_wallet_id' => $walletFrom[0]->id,
                        'to_wallet_id' => $walletTo[0]->id,
                        'amount' => trim($replyMessage[3], ' '),
                        'note' => trim($replyMessage[4], ' ')
                    ]);

                    $this->transferWallet($transfer->fromWallet, $transfer->toWallet, $transfer->amount, $transfer->note);
                    DB::commit();

                    $this->sendMessage($chatId, $this->unicodeToUtf8($success, 'Transfer berhasil'));
                } catch (Exception $e) {
                    DB::rollBack();
                    $this->sendMessage($chatId, $this->unicodeToUtf8($failed, $e->getMessage()));
                }
                break;
            case '/user':
                $this->sendMessage($chatId,  $this->formatText('%s'.PHP_EOL, $command));
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

    public function getWallet($names)
    {
        return Wallet::whereIn('name', $names)->get();
    }

    public function getCategory($name)
    {
        return Category::where('name', $name)->first();
    }

    public function getTransaction($wallet_ids, $date, $tipe)
    {
        $result = Transaction::whereIn('wallet_id', $wallet_ids)->where('type', $tipe);
        if ($date != ' all') {
            $result->whereDate('created_at', $date);
        }
        return $result;
    }

    public function whiteSpaces($value)
    {
        $result = preg_replace('/\s+/', ' ', $value);
        return $result;
    }
}
