<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBIgrTransactionHeader extends Model
{
    protected $table = "igr_transaction_headers";
    protected $fillable =
    [
        'id',
        'user_id',
        'trx_date',
        'trx_no',
        'cashier',
        'station',
        'flag_sync',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function users()
    {
        return $this->belongsTo('App\Models\TDBUser','user_id');
    }

    public function igrTransactionDetails()
    {
        return $this->hasMany('App\Models\TDBIgrTransactionDetail','igr_transaction_id');
    }
}
