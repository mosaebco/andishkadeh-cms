<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['amount_toman', 'status', 'gateway_reference', 'verified_at'])]
class DonationTransaction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount_toman' => 'integer',
            'verified_at' => 'datetime',
        ];
    }
}
