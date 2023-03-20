<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $fillable = [
        'wallet_id',
        'amount',
        'note',
        'type',
        'created_by',
        'updated_by',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_transactions');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (Transaction $transaction) {
            $transaction->created_by = auth()->user()->id ?? User::first()->id;
        });

        static::updating(function (Transaction $transaction) {
            $transaction->updated_by = auth()->user()->id ?? User::first()->id;
        });
    }
}
