<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Carbon $date
 */
class NameDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_id',
        'date',
        'is_main',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function name(): BelongsTo
    {
        return $this->belongsTo(Name::class);
    }

    public function getDateString(): string
    {
        return $this->date->format('m-d');
    }
}
