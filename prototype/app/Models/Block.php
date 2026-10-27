<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    /** @use HasFactory<\Database\Factories\BlockFactory> */
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'type',
        'position',
        'is_visible',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'data' => 'array',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
