<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->is_active || $subtotal < $this->min_order_amount) {
            return 0.00;
        }

        if ($this->discount_type === 'percentage') {
            return round(($subtotal * ($this->discount_value / 100)), 2);
        }

        return min($this->discount_value, $subtotal);
    }
}
