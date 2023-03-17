<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTransaction extends Model
{
    use HasFactory;

    public $fillable = [
        'category_id',
        'transaction_id',
    ];
}
