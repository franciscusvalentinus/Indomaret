<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBDevice extends Model
{
    protected $table = "devices";
    protected $fillable =
    [
        'id',
        'device_id',
        'name',
        'model',
        'sdk',
        'imei',
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
        return $this->hasMany('App\Models\TDBUser','device_id');
    }
}
