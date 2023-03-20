<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\TransactionTrait;

class TransactionController extends Controller
{
    use TransactionTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wallets = Wallet::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $transactions = Transaction::latest()->paginate(10);

        return view('admin.transaction.index', compact('transactions', 'wallets', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::create($request->validated());
            $transaction->categories()->attach($request->category_id);
            $this->updateWalletBalance($transaction->wallet, $transaction->amount, $transaction->type, $transaction->note);

            DB::commit();

            return redirect()->route('transaction.index')->with('success', 'Transaksi Berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return view('admin.transaction.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin.transaction.edit', compact('transaction', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            if($transaction->amount != $request->amount){
                $transaction->categories()->detach(); // hapus transaksi category
                $wallet = Wallet::find($request->wallet_id); // mengambil data dompet
                $wallet->increment('balance', $request->old_amount); // mengembalikan uang pada trannsaksi ke dompet

                $wallet->histories()->where('wallet_id', $wallet->id)->where('type', $transaction->type)
                                                ->where('amount', $transaction->amount)->update([
                                                    'amount' => $request->amount,
                                                    'note' => $request->note
                                                ]); // update history transaksi
                $transaction->update([
                            'amount' => $request->amount
                            ]); // update amount transaksi
                $transaction->categories()->attach($request->category_id);
                $wallet->decrement('balance', $request->amount); // update balance dompet
            }

            DB::commit();

            return redirect()->route('transaction.index')->with('success', 'Transaksi Berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
