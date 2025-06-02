<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TDBEmployees extends Model
{
    use SoftDeletes;
    
    protected $table = "employees";
    

    protected $fillable = [
        'rec_letter_number','ticket_number','nik','name','unit_name',
        'unit_id','branch_name','branch_id','job_position',
        'path_proposal','va_number','bank_id'
    ];
    protected $dates = ['deleted_at'];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}
