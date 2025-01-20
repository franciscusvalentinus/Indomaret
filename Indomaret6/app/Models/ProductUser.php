<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUser extends Model
{
    use HasFactory;

    protected $table = 'product_user2';

    protected $guarded = [
        'id'
    ];

    public function products(){
        return $this->belongsTo(Product::class, 'produk_id', 'id');
    }

    public function users(){
        return $this->belongsTo(User2::class, 'user_id', 'id');
    }
}
