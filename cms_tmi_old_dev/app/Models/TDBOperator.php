<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBOperator extends Model
{
    protected $table = "operators";
    protected $fillable =
    [
        'id',
        'user_id',
        'code',
        'name',
        'role',
        'pin',
        'phone_number',
        'address',
        'created_at',
        'updated_at',
        'deleted_at'
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

    public function shiftReports()
    {
        return $this->hasMany('App\Models\TDBShiftReport','operator_id');
    }

    public function pbHeaders()
    {
        return $this->hasMany('App\Models\TDBPbHeader','operator_id');
    }

    public function logs()
    {
        return $this->hasMany('App\Models\TDBLog','operator_id');
    }
}
