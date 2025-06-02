<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TDBOauthAccessToken extends Model
{
    protected $table = "oauth_access_tokens";
    protected $fillable =
        [
            'id', 'user_id', 'client_id', 'name', 'scopes', 'revoked', 'expires_at'
        ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('cms_config.connection_tmi_api');
        parent::__construct($attributes);
    }
}