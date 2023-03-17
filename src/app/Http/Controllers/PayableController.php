<?php

namespace App\Http\Controllers;

use App\Enums\TypeStatusEnum;
use App\Http\Requests\PayableRequest;
use App\Http\Requests\PaymentRequest;
use App\Models\Payable;
use App\Models\PayableHistory;
use App\Models\Wallet;
use App\Traits\TransactionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayableController extends Controller
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
        $data['payableExists'] = Payable::where('amount', '>', 0)->get();
        $data['payables'] = Payable::where('amount', '>', 0)->latest()->paginate(10);

        return view('admin.payable.index', $data);
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
    public function store(PayableRequest $request)
    {
        DB::beginTransaction();
        try {
            Payable::create($request->validated());

            DB::commit();

            return redirect()->route('payable.index')->with('success', 'Hutang Berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payable  $payable
     * @return \Illuminate\Http\Response
     */
    public function show(Payable $payable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payable  $payable
     * @return \Illuminate\Http\Response
     */
    public function edit(Payable $payable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payable  $payable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payable $payable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payable  $payable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payable $payable)
    {
        //
    }

    public function payment(PaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            $payment = PayableHistory::create($request->except('payment_amount') + [
                'amount' => $request->payment_amount
            ]);

            $payment->payable->decrement('amount', $payment->amount);

            $this->updateWalletBalance($payment->wallet, $payment->amount, TypeStatusEnum::pengeluaran(), $payment->note);

            DB::commit();

            return redirect()->route('payable.index')->with('success', 'Pemabayaran Hutang Berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
