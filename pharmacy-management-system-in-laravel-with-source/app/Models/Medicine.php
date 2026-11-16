<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'category_id', 'name', 'generic_name', 'barcode',
        'price', 'stock', 'reorder_level', 'expiry_date',
    ];

    protected $casts = ['expiry_date' => 'date'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereDate('expiry_date', '<', today());
    }

    public function scopeExpiringSoon(Builder $query, int $days = 60): Builder
    {
        return $query->whereDate('expiry_date', '>=', today())
            ->whereDate('expiry_date', '<=', today()->addDays($days));
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'reorder_level');
    }

    public function getStatusAttribute(): string
    {
        if ($this->expiry_date->isPast()) {
            return 'expired';
        }
        if ($this->expiry_date->lte(today()->addDays(60))) {
            return 'expiring';
        }

        return $this->stock <= $this->reorder_level ? 'low' : 'ok';
    }
}
