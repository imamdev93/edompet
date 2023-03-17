<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletHistory extends Model
{
    use HasFactory;

    public $fillable = [
        'wallet_id',
        'type',
        'amount',
        'note'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
