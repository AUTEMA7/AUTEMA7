<?php
/**
 * IlaraNet Bénin - Configuration principale
 * Plateforme Numérique de Gestion des Centres de Santé
 * République du Bénin — 2026
 */

return [
    // Application
    'app_name' => 'IlaraNet Bénin',
    'app_version' => '1.0.0',
    'app_env' => getenv('APP_ENV') ?: 'development',
    'app_debug' => getenv('APP_DEBUG') ?: true,
    'app_url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone' => 'Africa/Porto-Novo', // WAT - West Africa Time
    
    // Base de données
    'database' => [
        'driver' => getenv('DB_DRIVER') ?: 'mysql',
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'ilaranet_benin',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => 'ilara_',
    ],
    
    // Sécurité
    'security' => [
        'encryption_key' => getenv('ENCRYPTION_KEY') ?: 'base64:' . base64_encode(random_bytes(32)),
        'hash_cost' => 12,
        'session_lifetime' => 900, // 15 minutes pour les dossiers patients
        'code_service_length' => 6,
        'code_patient_min' => 6,
        'code_patient_max' => 8,
        'code_director_length' => 8,
        'otp_expiry' => 300, // 5 minutes
        'max_login_attempts' => 5,
    ],
    
    // Cache
    'cache' => [
        'driver' => 'file',
        'prefix' => 'ilaranet_',
        'default_ttl' => 3600,
    ],
    
    // Sessions
    'session' => [
        'driver' => 'file',
        'lifetime' => 120,
        'path' => __DIR__ . '/../storage/sessions',
        'cookie_name' => 'ilaranet_session',
    ],
    
    // Logs
    'logging' => [
        'driver' => 'file',
        'path' => __DIR__ . '/../storage/logs/',
        'level' => 'debug',
        'channels' => ['daily', 'error', 'audit'],
    ],
    
    // API Externes
    'api' => [
        'anip' => [
            'enabled' => true,
            'base_url' => getenv('ANIP_API_URL') ?: 'https://api.anip.bj/v1',
            'api_key' => getenv('ANIP_API_KEY') ?: '',
            'timeout' => 30,
        ],
        'cnss' => [
            'enabled' => true,
            'base_url' => getenv('CNSS_API_URL') ?: 'https://api.cnss.bj/v1',
            'api_key' => getenv('CNSS_API_KEY') ?: '',
        ],
        'mtn_momo' => [
            'enabled' => true,
            'base_url' => getenv('MTN_API_URL') ?: 'https://sandbox.momodeveloper.mtn.com',
            'api_key' => getenv('MTN_API_KEY') ?: '',
            'client_id' => getenv('MTN_CLIENT_ID') ?: '',
            'client_secret' => getenv('MTN_CLIENT_SECRET') ?: '',
        ],
        'celtiis' => [
            'enabled' => true,
            'base_url' => getenv('CELTIIS_API_URL') ?: 'https://api.celtiiscash.com',
            'api_key' => getenv('CELTIIS_API_KEY') ?: '',
        ],
        'flooz' => [
            'enabled' => true,
            'base_url' => getenv('FLOOZ_API_URL') ?: 'https://api.flooz.africa',
            'api_key' => getenv('FLOOZ_API_KEY') ?: '',
        ],
        'dhish2' => [
            'enabled' => true,
            'base_url' => getenv('DHIS2_URL') ?: '',
            'username' => getenv('DHIS2_USER') ?: '',
            'password' => getenv('DHIS2_PASS') ?: '',
        ],
    ],
    
    // Rôles et Permissions
    'roles' => [
        'super_admin' => 1,
        'directeur' => 2,
        'chef_service' => 3,
        'medecin' => 4,
        'infirmier' => 5,
        'sage_femme' => 6,
        'biologiste' => 7,
        'radiologue' => 8,
        'pharmacien' => 9,
        'comptable' => 10,
        'accueil' => 11,
        'patient' => 100,
    ],
    
    // Types d'établissements
    'facility_types' => [
        'hopital' => 'Hôpital',
        'clinique_privee' => 'Clinique Privée',
        'cabinet_medical' => 'Cabinet Médical',
        'centre_de_sante' => 'Centre de Santé',
        'polyclinique' => 'Polyclinique',
    ],
    
    // Types de services
    'service_types' => [
        'consultation' => 'Consultation Générale',
        'urgences' => 'Urgences',
        'hospitalisation' => 'Hospitalisation',
        'laboratoire' => 'Laboratoire',
        'imagerie' => 'Imagerie',
        'maternite' => 'Maternité',
        'pharmacie_interne' => 'Pharmacie Interne',
        'bloc_operatoire' => 'Bloc Opératoire',
        'pediatrie' => 'Pédiatrie',
        'comptabilite' => 'Comptabilité',
        'rh' => 'Ressources Humaines',
        'accueil' => 'Accueil / Administration',
    ],
    
    // Niveaux de triage ESI (Emergency Severity Index)
    'esi_levels' => [
        1 => ['color' => 'red', 'label' => 'Urgence Vitale', 'delay' => 'Immédiat'],
        2 => ['color' => 'orange', 'label' => 'Urgence Très Grave', 'delay' => '< 10 min'],
        3 => ['color' => 'yellow', 'label' => 'Urgence Grave', 'delay' => '< 60 min'],
        4 => ['color' => 'green', 'label' => 'Urgence Moyenne', 'delay' => '< 120 min'],
        5 => ['color' => 'blue', 'label' => 'Urgence Non Urgente', 'delay' => '< 240 min'],
    ],
    
    // Codes CIM-11 (OMS)
    'cim11' => [
        'enabled' => true,
        'api_url' => 'https://icd.who.int/browse11/l-m/en',
    ],
    
    // Upload
    'upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'dicom', 'dcm'],
        'paths' => [
            'patients' => 'uploads/patients/',
            'documents' => 'uploads/documents/',
            'imagerie' => 'uploads/imagerie/',
            'signatures' => 'uploads/signatures/',
        ],
    ],
    
    // Pagination
    'pagination' => [
        'default_per_page' => 20,
        'max_per_page' => 100,
    ],
    
    // Fuseau horaire Afrique de l'Ouest
    'date_format' => 'd/m/Y H:i:s',
    'datetime_format' => 'Y-m-d H:i:s',
];
