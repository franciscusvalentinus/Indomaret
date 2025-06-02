<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBEvent extends Model
{
    protected $table = "events";
    protected $fillable =
    [
        'id',
        'event_type',
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
        return $this->hasMany('App\Models\TDBLog','event_id');
    }
}
