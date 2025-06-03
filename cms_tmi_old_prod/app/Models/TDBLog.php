<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBLog extends Model
{
    protected $table = "logs";
    protected $fillable =
    [
        'id',
        'operator_id',
        'event_id',
        'event_status_id',
        'version_id',
        'sub_event',
        'description',
        'endpoint',
        'content',
        'event_time',
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

    public function events()
    {
        return $this->belongsTo('App\Models\TDBEvents','event_id');
    }

    public function event_statuses()
    {
        return $this->belongsTo('App\Models\TDBEventStatus','event_status_id');
    }

    public function versions()
    {
        return $this->belongsTo('App\Models\TDBVersion','version_id');
    }
}
