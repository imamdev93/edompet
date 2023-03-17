<?php

namespace App\Traits;

use App\Enums\TypeStatusEnum;
use App\Models\WalletHistory;

trait TransactionTrait
{
    public function updateWalletBalance($wallet, $amount, $type, $note)
    {
        if ($type == TypeStatusEnum::pemasukan()) {
            $wallet->increment('balance', $amount);
        } else {
            $wallet->decrement('balance', $amount);
        }

        $payload = [
            'wallet_id' => $wallet->id,
            'type' => $type,
            'amount' => $amount,
            'note' => $note,
        ];

        $this->setWalletHistory($payload);
    }

    private function setWalletHistory($payload)
    {
        WalletHistory::create($payload);
    }

    public function transferWallet($fromWallet, $toWallet, $amount, $note)
    {
        $this->updateWalletBalance($fromWallet, $amount, TypeStatusEnum::pengeluaran(), $note);
        $this->updateWalletBalance($toWallet, $amount, TypeStatusEnum::pemasukan(), $note);
    }
}
