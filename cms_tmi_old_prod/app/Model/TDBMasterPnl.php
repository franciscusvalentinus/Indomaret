<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TDBMasterPnl extends Model
{
    protected $table = "master_pnls";
    protected $fillable = [
        'type', 'flag_in_out'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
