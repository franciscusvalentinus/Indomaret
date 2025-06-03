<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBProduct extends Model
{
    protected $table = "products";
    protected $fillable =
    [
        'id',
        'tag_id',
        'category_id',
        'branch_id',
        'tmi_type_id',
        'plu',
        'description',
        'price',
        'unit',
        'conversion',
        'min_order',
        'min_qty',
        'max_qty',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function tags()
    {
        return $this->belongsTo('App\Models\TDBTag','tag_id');
    }

    public function categories()
    {
        return $this->belongsTo('App\Models\TDBCategory','category_id');
    }

    public function branches()
    {
        return $this->belongsTo('App\Models\TDBBranch','branch_id');
    }

    public function tmiTypes()
    {
        return $this->belongsTo('App\Models\TDBTmiType','tmi_type_id');
    }

    public function barcodes()
    {
        return $this->hasMany('App\Models\TDBBarcode','product_id');
    }

    public function userProducts()
    {
        return $this->hasMany('App\Models\TDBUserProduct','product_id');
    }

    public function igrTransactionDetails()
    {
        return $this->hasMany('App\Models\TDBIgrTransactionDetail','user_product_id');
    }

    public function pbDetails()
    {
        return $this->hasMany('App\Models\TDBPbDetail','product_id');
    }
}
