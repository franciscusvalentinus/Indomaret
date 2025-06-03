<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterToko extends Model
{
    protected $table = "master_toko";
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_cms');
        parent::__construct($attributes);
    }
}
