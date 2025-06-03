<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBUserBarcode extends Model
{
    protected $table = "user_barcodes";
    protected $fillable =
    [
        'id',
        'user_product_id',
        'barcode',
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
