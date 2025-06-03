<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBMasterPlu extends Model
{
    protected $table = "master_plus";
    protected $fillable =
    [
        'id',
        'plu',
        'description',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
