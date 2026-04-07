<?php

namespace App\Services;

use App\Models\User;

class SSOTokenService
{
    private string $secret;
    private int $ttlSeconds = 300; // 5 minutes — token must arrive within this window

    public function __construct()
    {
        $this->secret = env('SSO_SHARED_SECRET', '');
    }

    /**
     * Decode token, verify signature, fetch user from dashboard_db.users.
     * Returns the User on success, throws on failure.
     */
    public function decodeAndLogin(string $token): User
    {
        // Token format: base64(json_payload).HMAC
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Invalid token format.');
        }

        [$encodedPayload, $signature] = $parts;

        // Verify HMAC
        if (!hash_equals(hash_hmac('sha256', $encodedPayload, $this->secret), $signature)) {
            throw new \InvalidArgumentException('Invalid token signature.');
        }

        $payload = json_decode(base64_decode($encodedPayload), true);

        if (!$payload || !isset($payload['user_id'], $payload['timestamp'])) {
            throw new \InvalidArgumentException('Invalid token payload.');
        }

        // Reject tokens older than 5 minutes
        if (time() - $payload['timestamp'] > $this->ttlSeconds) {
            throw new \InvalidArgumentException('Token expired.');
        }

        // Fetch real user from dashboardv2 DB
        $user = User::find($payload['user_id']);
        if (!$user) {
            throw new \InvalidArgumentException('User not found.');
        }

        return $user;
    }

    /**
     * Generate a token — used by dashboardv2 to build the SSO redirect URL.
     */
    public function generateToken(int $userId): string
    {
        $payload        = ['user_id' => $userId, 'timestamp' => time()];
        $encodedPayload = base64_encode(json_encode($payload));
        $signature      = hash_hmac('sha256', $encodedPayload, $this->secret);
        return $encodedPayload . '.' . $signature;
    }
}
