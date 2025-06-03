<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBVersion extends Model
{
    protected $table = "version";
    protected $fillable =
    [
        'id',
        'name',
        'desc',
        'flag_lastest',
        'updated_on',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function logs()
    {
        return $this->hasMany('App\Models\TDBLog','version_id');
    }
}
