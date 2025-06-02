<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TDBUser extends Model
{
    use SoftDeletes;

    protected $table = "users";
    protected $softDelete = true;
    protected $fillable =
    [
        'id',
        'branch_id',
        'device_id',
        'tmi_type_id',
        'role',
        'member_code',
        'name',
        'store_name',
        'email',
        'phone_number',
        'address',
        'password',
        'flag_active',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    protected $dates = ['deleted_at'];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function branches()
    {
        return $this->belongsTo('App\Models\TDBBranch','branch_id');
    }

    public function tmiTypes()
    {
        return $this->belongsTo('App\Models\TDBTmiType','tmi_type_id');
    }

    public function devices()
    {
        return $this->belongsTo('App\Models\TDBDevice','device_id');
    }

    public function userProducts()
    {
        return $this->hasMany('App\Models\TDBUserProduct','user_id');
    }

    public function trxHeaders()
    {
        return $this->hasMany('App\Models\TDBTrxHeader','user_id');
    }

    public function igrTransactionHeaders()
    {
        return $this->hasMany('App\Models\TDBIgrTransactionHeader','user_id');
    }

    public function pbHeaders()
    {
        return $this->hasMany('App\Models\TDBPbHeader','user_id');
    }

    public function operators()
    {
        return $this->hasMany('App\Models\TDBOperator','user_id');
    }

    public function otps()
    {
        return $this->hasOne('App\Models\TDBOtp','user_id');
    }
}
