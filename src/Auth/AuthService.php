<?php

namespace App\Auth;

use App\User\User;

class AuthService
{
    /**
     * @param array $data
     * @throws \Exception
     */
    public function sign(array $data = [])
    {
        if (empty($data['email'])) {
            throw new \Exception('E-mail is required', 422);
        }
        if (empty($data['password'])) {
            throw new \Exception('Password is required', 422);
        }

        /** @var User $user */
        $user = User::findByEmail($data['email']);
        if (empty($user) || !$this->validateUser($user, $data)) {
            throw new \Exception('E-mail or Password incorrect', 401);
        }

        /** @var User $user */
        $user = User::find($user->id);

        return ['token' => self::generateToken($user)];
    }

    public function validateUser(User $user, array $data = []): bool
    {
        return password_verify($data['password'], $user->getPassword());
    }

    public static function generateToken(User $user)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $configToken = include 'token_config.php';
        $header = json_encode([
            'typ' => $configToken['typ'],
            'alg' => $configToken['alg'],
        ]);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $user->expired_at = strtotime($configToken['expire_time']);
        $payload = json_encode($user);
        $base64UrlPayload = str_replace(['+', '/', '=', '-'], ['-', '_', '', '+'], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $configToken['key'], true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function decodeToken(string $token): User
    {
        $parts = explode('.', $token);
        $payload = str_replace('\\', '', base64_decode($parts[1]));
        return new User(json_decode($payload, true));
    }

    /**
     * @param $expiredAt
     * @return bool
     * @throws \Exception
     */
    public static function validateExpiredAt($expiredAt)
    {
        if (strtotime(date('Y-m-d H:i:s')) > $expiredAt) {
            throw new \Exception('Expired token', 401);
        }
        return true;
    }
}