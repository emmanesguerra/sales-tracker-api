<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\TenantScope;
use App\Traits\UserStamp;

class Item extends Model
{
    use HasFactory, SoftDeletes, UserStamp;

    protected static function booted()
    {
        static::addGlobalScope(new TenantScope());
    }

    protected $fillable = [ 
        'tenant_id',
        'code',
        'name',
        'description',
        'price',
        'stock',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
