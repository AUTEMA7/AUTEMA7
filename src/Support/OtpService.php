<?php

declare(strict_types=1);

namespace App\Support;

use App\Database;

final class OtpService
{
    public static function issue(int $userId, string $phone): string
    {
        $code = (string) random_int(100000, 999999);
        $stmt = Database::connection()->prepare('INSERT INTO otp_codes (user_id, otp_code, expires_at) VALUES (:user_id, :otp_code, DATE_ADD(NOW(), INTERVAL 5 MINUTE))');
        $stmt->execute([
            'user_id' => $userId,
            'otp_code' => password_hash($code, PASSWORD_BCRYPT),
        ]);

        SmsGateway::send($phone, "Votre code OTP IlaraNet est: {$code}");
        return $code;
    }

    public static function verify(int $userId, string $code): bool
    {
        $stmt = Database::connection()->prepare('SELECT id, otp_code FROM otp_codes WHERE user_id = :user_id AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($code, (string) $row['otp_code'])) {
            return false;
        }

        $update = Database::connection()->prepare('UPDATE otp_codes SET used_at = NOW() WHERE id = :id');
        $update->execute(['id' => $row['id']]);
        return true;
    }
}
