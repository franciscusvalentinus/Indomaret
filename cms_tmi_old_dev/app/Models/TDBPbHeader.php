<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBPbHeader extends Model
{
    protected $table = "pb_headers";
    protected $fillable =
    [
        'id',
        'operator_id',
        'user_id',
        'status_id',
        'name',
        'email',
        'address',
        'phone_number',
        'po_number',
        'po_date',
        'total_items',
        'total_items_order',
        'total_items_fulfilled',
        'qty_order',
        'price_order',
        'qty_fulfilled',
        'price_fulfilled',
        'flag_free_delivery',
        'flag_sent',
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

    public function operators()
    {
        return $this->belongsTo('App\Models\TDBOperator','operator_id');
    }

    public function statuses()
    {
        return $this->belongsTo('App\Models\TDBStatus', 'status_id');
    }

    public function pbDetails()
    {
        return $this->hasMany('App\Models\TDBPbDetail', 'pb_header_id');
    }
}
