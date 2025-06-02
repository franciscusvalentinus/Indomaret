<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBEventStatus extends Model
{
    protected $table = "event_statuses";
    protected $fillable =
    [
        'id',
        'event_status',
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
        return $this->hasMany('App\Models\TDBLog','event_status_id');
    }
}
