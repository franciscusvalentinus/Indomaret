<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBTrxHeader extends Model
{
    protected $table = "trx_headers";
    protected $fillable =
    [
        'id',
        'user_id',
        'shift_id',
        'trx_no',
        'qty',
        'price',
        'tax',
        'discount',
        'grand_total',
        'margin',
        'cost',
        'trx_date',
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
        return $this->belongsTo('App\Models\TDBUser', 'user_id');
    }

    public function shifts()
    {
        return $this->belongsTo('App\Models\TDBShiftReport', 'shift_id');
    }

    public function trxDetails()
    {
        return $this->hasMany('App\Models\TDBTrxDetail', 'trx_header_id');
    }
}
