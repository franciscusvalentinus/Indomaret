<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDBOtp extends Model
{
    protected $table = "otps";
    protected $fillable =
    [
        'id',
        'user_id',
        'otp',
        'created_at',
        'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }

    public function users()
    {
        return $this->belongsTo('App\Models\TDBUser', 'user_id');
    }
}
