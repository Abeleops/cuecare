<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['customer_code', 'full_name', 'phone', 'email'])]
class Customer extends Model
{
    use SoftDeletes;

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
