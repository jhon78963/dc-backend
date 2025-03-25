<?php

namespace App\Product\Models;

use App\Role\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'brand_id',               
        'category_id',          
        'measurement_unit_id',    
        'measurement_unit_name', 
        'name',                   
        'barcode',               
        'barcode_path',          
        'sale_price',             
        'purchase_price',       
        'minimum_stock',  
        //'internal_code',      
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'creation_time',
        'creator_user_id',
        'last_modification_time',
        'last_modifier_user_id',
        'is_deleted',
        'deleter_user_id',
        'deletion_time',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            // 'password'          => 'hashed',
        ];
    }

    /*
    public function role(): BelongsTo {
        return $this->belongsTo(Role::class);
    }
    */
}
