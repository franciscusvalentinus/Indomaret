<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $table = 'cms_tmi.customer_addresses';

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_cms');
        parent::__construct($attributes);
    }
}
