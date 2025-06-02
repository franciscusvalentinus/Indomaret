<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBShiftReport extends Model
{
    protected $table = "shift_reports";
    protected $fillable =
    [
        'id',
        'operator_id',
        'date',
        'shift',
        'notes',
        'beg_balance',
        'end_balance',
        'open_shift',
        'close_shift',
        'sales',
        'sales_amt',
        'margin',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function operators()
    {
        return $this->belongsTo('App\Models\TDBOperator','operator_id');
    }

    public function trxHeaders()
    {
        return $this->hasMany('App\Models\TDBTrxHeader','shift_id');
    }
}
