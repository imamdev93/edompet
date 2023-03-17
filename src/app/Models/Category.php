<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'slug',
        'color'
    ];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'category_transactions')->orderByDesc('created_at')->groupBy('wallet_id')->select(DB::raw('sum(amount) as total'), 'wallet_id');
    }

    public function countTransaction($date, $type)
    {
        return $this->transactions()->whereMonth('created_at', $date->format('m'))->whereYear('created_at', $date->format('Y'))->where('type', $type)->sum('amount');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (Category $category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function (Category $category) {
            $category->slug = Str::slug($category->name);
        });
    }
}
