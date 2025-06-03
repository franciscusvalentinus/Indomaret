<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBPbStatus extends Model
{
    protected $table = "pb_statuses";
    protected $fillable =
    [
        'id',
        'title',
        'description',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function pbHeaders()
    {
        return $this->hasMany('App\Models\TDBPbHeader','status_id');
    }
}
