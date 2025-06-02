<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBBarcode extends Model
{
    protected $table = "barcodes";
    protected $fillable =
    [
        'id',
        'product_id',
        'barcode',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function products()
    {
        return $this->belongsTo('App\Models\TDBProduct','product_id');
    }
}
