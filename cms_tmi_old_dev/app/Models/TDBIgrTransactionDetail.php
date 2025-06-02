<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBIgrTransactionDetail extends Model
{
    protected $table = "igr_transaction_details";
    protected $fillable =
    [
        'id',
        'igr_transaction_id',
        'user_product_id',
        'price',
        'quantity',
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
        return $this->belongsTo('App\Models\TDBProduct','user_product_id');
    }

    public function igrTransactionHeaders()
    {
        return $this->belongsTo('App\Models\TDBIgrTransactionHeader','igr_transaction_id');
    }
}
