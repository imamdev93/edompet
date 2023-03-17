<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    use HasFactory;
    use HasFactory;

    public $fillable = [
        'wallet_id',
        'title',
        'description',
        'amount',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function histories()
    {
        return $this->hasMany(ReceivableHistory::class)->orderBy('created_at', 'desc');
    }
}
