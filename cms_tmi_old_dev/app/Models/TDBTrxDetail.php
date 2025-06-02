<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBTrxDetail extends Model
{
    protected $table = "trx_details";
    protected $fillable =
    [
        'id',
        'trx_header_id',
        'user_product_id',
        'promotion_id',
        'qty',
        'price',
        'tax',
        'discount',
        'sub_total',
        'margin',
        'cost',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function trxHeaders()
    {
        return $this->belongsTo('App\Models\TDBTrxHeader','trx_header_id');
    }

    public function userproducts()
    {
        return $this->belongsTo('App\Models\TDBUserProduct','user_product_id');
    }

    public function promotions()
    {
        return $this->belongsTo('App\Models\TDBPromotion','promotion_id');
    }
}
