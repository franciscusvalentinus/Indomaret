<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TDBPnlTrxHeader extends Model
{
    protected $table = "pnl_trx_headers";
    protected $fillable = ['user_id', 'datetime', 'trx_code', 'total_income', 'total_spending'];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
