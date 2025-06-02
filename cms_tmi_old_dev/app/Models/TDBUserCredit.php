<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TDBUserCredit extends Model
{
    use SoftDeletes;

    protected $table = "user_credits";
    protected $fillable = [
        'user_id', 'nik', 'identity_number', 'npwp_number', 'pkp', 'credit_number', 'credit_status_id', 'credit_type_id', 'credit_limit', 
        'real_order', 'tenor', 'user_approval_token_id', 'last_order_period', 'start_period', 'end_period', 'grace_period', 'description',
        'approved_by', 'approved_date', 'va_number', 'max_invoice_date',
        'monitoring_period', 'flag_take_over', 'installments'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
