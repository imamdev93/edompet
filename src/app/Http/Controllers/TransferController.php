<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Models\Transfer;
use App\Models\Wallet;
use App\Traits\TransactionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
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
        $transfers = Transfer::latest()->paginate(10);

        return view('admin.transfer.index', compact('transfers', 'wallets'));
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
    public function store(TransferRequest $request)
    {
        DB::beginTransaction();
        try {
            $transfer = Transfer::create($request->validated());

            $this->transferWallet($transfer->fromWallet, $transfer->toWallet, $transfer->amount, $transfer->note);

            DB::commit();

            return redirect()->route('transfer.index')->with('success', 'Transfer Berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
