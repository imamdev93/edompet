<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'icon',
        'description',
        'balance',
        'type',
    ];

    public function histories()
    {
        return $this->hasMany(WalletHistory::class)->orderBy('created_at', 'desc');
    }

    public function historyByType($type)
    {
        return $this->histories()->where('type', $type);
    }

    public function historyByDate($type, $condition = 'whereDate', $date = null)
    {
        $date = $date ?? date('Y-m-d');
        return $this->historyByType($type)->$condition('created_at', $date);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function fromWallets()
    {
        return $this->hasMany(Transfer::class, 'from_wallet_id', 'id');
    }

    public function toWallets()
    {
        return $this->hasMany(Transfer::class, 'to_wallet_id', 'id');
    }
}
