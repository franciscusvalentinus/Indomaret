<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBPbDetail extends Model
{
    protected $table = "pb_details";
    protected $fillable =
    [
        'id',
        'pb_header_id',
        'product_id',
        'price_order',
        'qty_order',
        'price_fulfilled',
        'qty_fulfilled',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function pbHeaders()
    {
        return $this->belongsTo('App\Models\TDBPbHeader','pb_header_id');
    }

    public function products()
    {
        return $this->belongsTo('App\Models\TDBProduct','product_id');
    }
}
