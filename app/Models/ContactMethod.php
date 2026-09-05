<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['label', 'type', 'value', 'icon', 'sort_order', 'is_visible'])]
class ContactMethod extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
