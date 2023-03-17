<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use HasFactory;

    public $fillable = [
        'title',
        'description',
        'amount',
    ];

    public function histories()
    {
        return $this->hasMany(PayableHistory::class)->orderBy('created_at', 'desc');
    }
}
