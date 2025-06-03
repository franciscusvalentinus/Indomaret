<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBStockOpname extends Model
{
    protected $table = "stock_opnames";
    protected $fillable =
    [
        'id',
        'user_product_id',
        'trx_id',
        'last_stock',
        'current_stock',
        'difference_stock',
        'notes',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function userproducts()
    {
        return $this->belongsTo('App\Models\TDBUserProduct','user_product_id');
    }
}
