<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Name extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function nameDays(): HasMany
    {
        return $this->hasMany(NameDay::class);
    }

    public function getMainNameDays(): Collection
    {
        return $this->nameDays()->where('is_main', 1)->get();
    }
}
