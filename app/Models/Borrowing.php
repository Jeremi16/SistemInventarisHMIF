<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'item_name',
        'borrower_name',
        'borrower_nim',
        'start_date',
        'start_datetime',
        'end_date',
        'end_datetime',
        'purpose',
        'status',
        'admin_note',
        'handover_date',
        'handover_condition',
        'handover_photo',
        'return_date',
        'return_condition',
        'return_photo',
        'damage_description',
        'extension_requested',
        'extension_new_date',
        'extension_reason',
        'extension_rejection_reason',
        'extension_rejected_at',
        'fine_amount',
        'pre_return_condition',
        'pre_return_check_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'start_datetime' => 'datetime',
            'end_date' => 'date',
            'end_datetime' => 'datetime',
            'handover_date' => 'date',
            'return_date' => 'date',
            'extension_new_date' => 'date',
            'extension_rejected_at' => 'datetime',
            'pre_return_check_date' => 'date',
            'extension_requested' => 'boolean',
            'fine_amount' => 'decimal:2',
        ];
    }

    public function startDateTime()
    {
        return $this->start_datetime ?: $this->start_date;
    }

    public function endDateTime()
    {
        return $this->end_datetime ?: $this->end_date;
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(BorrowingNote::class);
    }
}
