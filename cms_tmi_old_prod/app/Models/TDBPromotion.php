<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBPromotion extends Model
{
    protected $table = "promotions";
    protected $fillable =
    [
        'id',
        'user_product_id',
        'mechanism',
        'start_date',
        'end_date',
        'min_purchase',
        'discount',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function userProducts()
    {
        return $this->belongsTo('App\Models\TDBUserProduct','user_product_id');
    }

    public function trxDetails()
    {
        return $this->hasMany('App\Models\TDBTrxDetail','promotion_id');
    }
}
