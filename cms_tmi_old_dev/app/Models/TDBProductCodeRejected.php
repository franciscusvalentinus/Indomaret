<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TDBProductCodeRejected extends Model
{
    use SoftDeletes;

    protected $table = 'product_code_rejecteds';
    protected $fillable = [
        'plu', 'branch_id'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
