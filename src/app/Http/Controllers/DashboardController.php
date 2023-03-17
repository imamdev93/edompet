<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Traits\DashboardTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use DashboardTrait;

    public function __invoke(Request $request)
    {
        $data['walletByType'] = Wallet::select('type', DB::raw('sum(balance) as total'))->groupBy('type')->orderBy('type', 'asc')->get();
        $data['wallets'] = Wallet::orderByDesc('balance')->limit(8)->get();
        $data['walletHistoryToday'] = Wallet::with('histories')->orderBy('name', 'asc')->get();
        $data['transactionByCategory'] = $this->getDataChart($request);
        $data['transactionByWallet'] = $this->getChartByWallet($request);
        $data['label'] = $data['transactionByCategory']['label'];
        $data['value'] = $data['transactionByCategory']['value'];
        $data['color'] = collect($data['transactionByCategory']['value'])->pluck('color')->toArray();
        $data['expense'] = Transaction::where('type', 'pengeluaran')->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('amount');
        $data['income'] = Transaction::where('type', 'pemasukan')->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('amount');
        $data['transaksi'] = $request->transaksi ?: 'pengeluaran';
        return view('admin.dashboard', $data);
    }

    private function getDataChart($request)
    {
        $categories = Category::query()->orderBy('name', 'asc')->get();
        $data = $this->transactionByCategory($categories, $request);

        return $data;
    }

    private function getChartByWallet($request)
    {
        $wallets = Wallet::query()->orderBy('name', 'asc')->get();
        $data = $this->transactionByWallet($wallets, $request);

        return $data;
    }
}
