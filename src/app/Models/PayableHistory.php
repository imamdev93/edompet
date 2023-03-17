<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayableHistory extends Model
{
    use HasFactory;

    public $fillable = [
        'payable_id',
        'wallet_id',
        'note',
        'amount',
    ];

    public function payable()
    {
        return $this->belongsTo(Payable::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
