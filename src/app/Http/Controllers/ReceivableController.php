<?php

namespace App\Http\Controllers;

use App\Enums\TypeStatusEnum;
use App\Http\Requests\ReceivableHistoryRequest;
use App\Http\Requests\ReceivableRequest;
use App\Models\Receivable;
use App\Models\ReceivableHistory;
use App\Models\Wallet;
use App\Traits\TransactionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivableController extends Controller
{
    use TransactionTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['wallets'] = Wallet::orderBy('name', 'asc')->get();
        $data['receivableExists'] = Receivable::where('amount', '>', 0)->get();
        $data['receivables'] = Receivable::where('amount', '>', 0)->latest()->paginate(10);

        return view('admin.receivable.index', $data);
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
    public function store(ReceivableRequest $request)
    {
        DB::beginTransaction();
        try {
            $receivable = Receivable::create($request->validated());

            $this->updateWalletBalance($receivable->wallet, $receivable->amount, TypeStatusEnum::pengeluaran(), $receivable->description);

            DB::commit();

            return redirect()->route('receivable.index')->with('success', 'Piutang Berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receivable  $receivable
     * @return \Illuminate\Http\Response
     */
    public function show(Receivable $receivable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Receivable  $receivable
     * @return \Illuminate\Http\Response
     */
    public function edit(Receivable $receivable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receivable  $receivable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receivable $receivable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receivable  $receivable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receivable $receivable)
    {
        //
    }

    public function payment(ReceivableHistoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $payment = ReceivableHistory::create($request->except('payment_amount') + [
                'amount' => $request->payment_amount
            ]);

            $payment->receivable->decrement('amount', $payment->amount);

            $this->updateWalletBalance($payment->receivable->wallet, $payment->amount, TypeStatusEnum::pemasukan(), $payment->note);

            DB::commit();

            return redirect()->route('receivable.index')->with('success', 'Pembayaran Piutang Berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
