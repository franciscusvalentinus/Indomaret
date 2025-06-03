<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TDBUserApprovalToken extends Model
{
    use SoftDeletes;

    protected $table = "user_approval_tokens";
    protected $fillable =
    [
        'user_approval_id',
        'token',
        'is_used', 'is_expired'
    ];
    protected $dates = ['deleted_at'];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
