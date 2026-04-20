<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Support\AuditLogger;
use App\Support\Auth;

final class IntegrationController
{
    public static function verifyAnip(): void
    {
        Auth::requireAuth();
        $npi = trim((string) ($_POST['npi'] ?? ''));
        $isValid = preg_match('/^[A-Z0-9\-]{6,40}$/', $npi) === 1;
        $_SESSION['flash_info'] = $isValid ? "NPI {$npi} validé (simulation ANIP)." : 'NPI invalide.';
        AuditLogger::log('ANIP_VERIFY', 'npi', $npi, ['valid' => $isValid]);
        header('Location: /patients');
    }

    public static function mobilePayment(): void
    {
        Auth::requireRole(['director', 'chief']);
        $amount = (float) ($_POST['amount'] ?? 0);
        $channel = (string) ($_POST['channel'] ?? 'momo');
        $reference = 'PAY-' . strtoupper(bin2hex(random_bytes(4)));

        $stmt = Database::connection()->prepare('INSERT INTO payments (tenant_id, amount, channel, reference, status) VALUES (:tenant, :amount, :channel, :reference, :status)');
        $stmt->execute([
            'tenant' => $_SESSION['user']['tenant_id'],
            'amount' => $amount,
            'channel' => $channel,
            'reference' => $reference,
            'status' => 'success_simulated',
        ]);

        AuditLogger::log('MOBILE_PAYMENT', 'payment', (string) Database::connection()->lastInsertId(), ['channel' => $channel, 'reference' => $reference]);
        $_SESSION['flash_info'] = "Paiement {$channel} simulé validé. Réf: {$reference}";
        header('Location: /dashboard');
    }
}
