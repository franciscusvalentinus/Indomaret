<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBLogSync extends Model
{
    protected $table = "log_syncs";
    protected $fillable =
        [
            'user_id', 'datetime', 'status'
        ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
