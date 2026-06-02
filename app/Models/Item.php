<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'status',
        'quantity',
        'condition',
        'location',
        'description',
        'photo',
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
            ? $query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('category', 'like', '%' . $term . '%')
                    ->orWhere('condition', 'like', '%' . $term . '%')
                    ->orWhere('location', 'like', '%' . $term . '%');
            })
            : $query;
    }

    public function scopeByStatus($query, $status)
    {
        return $status && $status !== 'all'
            ? $query->where('status', $status)
            : $query;
    }

    public function scopeByCondition($query, $condition)
    {
        return $condition && $condition !== 'all'
            ? $query->where('condition', $condition)
            : $query;
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }
}
