<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TDBPnlTrxDetail extends Model
{
    protected $table = "pnl_trx_details";
    protected $fillable = [
        'master_pnl_id', 'pnl_trx_header_id', 'currency'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
