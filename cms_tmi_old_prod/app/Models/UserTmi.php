<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTmi extends Model
{
    protected $table = "users";

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
