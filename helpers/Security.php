<?php
/**
 * IlaraNet Bénin - Security Helpers
 * Fonctions de sécurité: hash, validation, sanitization
 */

namespace IlaraNet\Helpers;

class Security
{
    /**
     * Hash un mot de passe
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Vérifie un mot de passe
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Génère un code PIN aléatoire (6-8 chiffres)
     */
    public static function generatePin(int $length = 6): string
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }

    /**
     * Génère un token unique
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Sanitize une entrée utilisateur
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valide un email
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valide un numéro de téléphone béninois
     */
    public static function isValidBeninPhone(string $phone): bool
    {
        // Formats: +229 XX XX XX XX, 229XXXXXXXX, XXXXXXXXXX
        $pattern = '/^(\+229|229)?[456][0-9]{7}$/';
        return preg_match($pattern, preg_replace('/\s/', '', $phone)) === 1;
    }

    /**
     * Valide un NPI ANIP (format béninois)
     */
    public static function isValidNPI(string $npi): bool
    {
        // Format: BN-YYYY-XXXXXX ou temporaire INCONNU-YYYY-XXXXXX
        $patterns = [
            '/^BN-\d{4}-\d{6}$/',
            '/^INCONNU-\d{4}-\d{6}$/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $npi) === 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Valide un code service (6 chiffres)
     */
    public static function isValidServiceCode(string $code): bool
    {
        return preg_match('/^\d{6}$/', $code) === 1;
    }

    /**
     * Valide un code patient (6-8 chiffres)
     */
    public static function isValidPatientCode(string $code): bool
    {
        return preg_match('/^\d{6,8}$/', $code) === 1;
    }

    /**
     * Chiffre une donnée sensible (AES-256)
     */
    public static function encrypt(string $data, string $key): string
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', hash('sha256', $key, true), 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Déchiffre une donnée
     */
    public static function decrypt(string $data, string $key): string
    {
        $decoded = base64_decode($data);
        $iv = substr($decoded, 0, 16);
        $encrypted = substr($decoded, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', hash('sha256', $key, true), 0, $iv);
    }

    /**
     * Vérifie une adresse IP
     */
    public static function getIpAddress(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
               $_SERVER['HTTP_CLIENT_IP'] ?? 
               $_SERVER['REMOTE_ADDR'] ?? 
               '0.0.0.0';
    }

    /**
     * Vérifie si l'IP est suspecte (pays étranger)
     */
    public static function isSuspiciousIp(string $ip): bool
    {
        // Liste des IPs locales et béninoises autorisées
        $allowedRanges = [
            '127.0.0.0/8',      // Localhost
            '10.0.0.0/8',       // Private
            '172.16.0.0/12',    // Private
            '192.168.0.0/16',   // Private
            // Ajouter les ranges d'IPs béninoises ici
        ];

        foreach ($allowedRanges as $range) {
            if (self::ipInRange($ip, $range)) {
                return false;
            }
        }
        
        // En production, retourner true pour les IPs hors Bénin
        return false; // Temporairement désactivé pour le dev
    }

    /**
     * Vérifie si une IP est dans un range
     */
    private static function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$subnet, $bits] = explode('/', $range);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $mask = -1 << (32 - (int)$bits);

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    /**
     * Génère un QR Code data pour patient
     */
    public static function generatePatientQRData(array $patient): string
    {
        $data = [
            'npi' => $patient['npi'],
            'nom' => $patient['nom'],
            'prenom' => $patient['prenom'],
            'telephone' => $patient['telephone'] ?? '',
            'contacts_urgence' => $patient['contacts_urgence'] ?? [],
            'medecin_traitant' => $patient['medecin_traitant'] ?? null
        ];
        
        return base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Valide un RCCM béninois
     */
    public static function isValidRCCM(string $rccm): bool
    {
        // Format: BJ COT 20XX B XXXXX
        $pattern = '/^BJ\s?[A-Z]{3}\s?\d{4}\s?[A-Z]\s?\d{5}$/i';
        return preg_match($pattern, $rccm) === 1;
    }

    /**
     * Nettoie un fichier uploadé
     */
    public static function sanitizeFilename(string $filename): string
    {
        $info = pathinfo($filename);
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $info['filename']);
        $extension = strtolower($info['extension'] ?? '');
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception("Type de fichier non autorisé");
        }
        
        return $basename . '.' . $extension;
    }

    /**
     * Vérifie la taille d'un fichier
     */
    public static function checkFileSize(int $size, int $maxBytes = 5242880): bool
    {
        return $size <= $maxBytes; // 5MB par défaut
    }
}
