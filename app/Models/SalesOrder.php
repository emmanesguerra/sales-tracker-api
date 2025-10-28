<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\TenantScope;
use App\Traits\UserStamp;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes, UserStamp;

    protected static function booted()
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($item) {
            if (auth()->check()) {
                // Attach tenant_id from the authenticated user
                $item->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    protected $fillable = [
        'tenant_id',
        'order_date',
        'order_time',
        'item_id',
        'item_price',
        'quantity',
        'total_amount',
    ];

    /**
     * Define the relationship between SalesOrder and Item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
