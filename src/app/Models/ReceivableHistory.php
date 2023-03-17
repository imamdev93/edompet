<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivableHistory extends Model
{
    use HasFactory;

    public $fillable = [
        'receivable_id',
        'note',
        'amount',
    ];

    public function receivable()
    {
        return $this->belongsTo(Receivable::class);
    }
}
