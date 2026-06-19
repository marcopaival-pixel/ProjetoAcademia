<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthAuditLog extends Model
{
    public const EVENT_LOGIN_SUCCESS = 'login_success';

    public const EVENT_LOGIN_FAILED = 'login_failed';

    public const EVENT_LOGOUT = 'logout';

    public const EVENT_PASSWORD_RESET = 'password_reset';

    public const EVENT_PASSWORD_RESET_REQUEST = 'password_reset_request';

    public const EVENT_API_TOKEN_ISSUED = 'api_token_issued';

    public const EVENT_API_TOKEN_FAILED = 'api_token_failed';

    public const EVENT_API_TOKEN_REVOKED = 'api_token_revoked';

    public const EVENT_OAUTH_LOGIN = 'oauth_login';

    public const EVENT_OAUTH_REGISTER = 'oauth_register';

    protected $fillable = [
        'user_id',
        'email',
        'event',
        'guard',
        'success',
        'ip',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'success' => 'boolean',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
