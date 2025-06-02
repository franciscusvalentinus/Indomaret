<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKodepos extends Model
{
    protected $table = 'cms_tmi.master_kodepos';

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_cms');
        parent::__construct($attributes);
    }
}
