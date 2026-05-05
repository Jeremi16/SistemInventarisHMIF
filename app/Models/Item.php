<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'status',
        'quantity',
        'condition',
        'location',
        'description',
    ];

    public function scopeByCategory($query, $category)
    {
        return $category && $category !== 'all'
            ? $query->where('category', $category)
            : $query;
    }

    public function scopeSearch($query, $term)
    {
        return $term
            ? $query->where('name', 'like', '%' . $term . '%')
            : $query;
    }
}
