<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $primaryKey = 'promotion_id';
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'used_count' => 'integer',
        'usage_limit' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scope để lấy khuyến mãi còn hiệu lực
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    // Scope để lấy khuyến mãi theo mã
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Kiểm tra khuyến mãi còn hiệu lực
    public function isValid()
    {
        return $this->is_active && 
               now()->between($this->start_date, $this->end_date) &&
               ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    // Tính toán giá trị giảm giá
    public function calculateDiscount($amount)
    {
        if (!$this->isValid() || $amount < $this->min_amount) {
            return 0;
        }

        $discount = 0;
        
        if ($this->discount_type === 'Percentage') {
            $discount = $amount * ($this->discount_value / 100);
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = $this->discount_value;
        }

        return min($discount, $amount);
    }

    // Tăng số lần sử dụng
    public function incrementUsage()
    {
        $this->increment('used_count');
    }
}