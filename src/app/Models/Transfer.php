<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    public $fillable = [
        'from_wallet_id',
        'to_wallet_id',
        'amount',
        'note',
        'created_by',
        'updated_by',
    ];

    public function fromWallet()
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id', 'id');
    }

    public function toWallet()
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (Transfer $transfer) {
            $transfer->created_by = auth()->user()->id ?? User::first()->id;
        });

        static::updating(function (Transfer $transfer) {
            $transfer->updated_by = auth()->user()->id ?? User::first()->id;
        });
    }
}
