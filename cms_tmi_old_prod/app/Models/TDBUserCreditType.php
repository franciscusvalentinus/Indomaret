<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBUserCreditType extends Model
{
    protected $table = "user_credit_types";
    protected $fillable = [
        'name'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
