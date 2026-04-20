<?php
/**
 * IlaraNet Bénin - Patient Model
 * Gestion des dossiers patients et NPI
 */

namespace IlaraNet\Models;

use IlaraNet\Core\Database;
use IlaraNet\Helpers\Security;
use IlaraNet\Helpers\AuditLogger;

class Patient
{
    private Database $db;
    private AuditLogger $auditLogger;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->auditLogger = new AuditLogger();
    }

    /**
     * Trouve un patient par NPI
     */
    public function findByNPI(string $npi): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM patient_files WHERE npi = ?",
            [$npi]
        );
    }

    /**
     * Trouve un dossier patient par ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM patient_files WHERE id = ?",
            [$id]
        );
    }

    /**
     * Trouve un dossier patient par numéro interne
     */
    public function findByInternalNumber(string $internalNumber): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM patient_files WHERE internal_number = ?",
            [$internalNumber]
        );
    }

    /**
     * Crée un nouveau dossier patient
     */
    public function create(array $data, int $facilityId): int
    {
        $this->db->beginTransaction();
        
        try {
            // Générer le code d'accès patient (PIN 6-8 chiffres)
            $patientCode = Security::generatePin(random_int(6, 8));
            
            // Générer le numéro interne
            $year = date('Y');
            $lastNumber = $this->db->fetch(
                "SELECT MAX(internal_number) as last FROM patient_files 
                 WHERE facility_id = ? AND internal_number LIKE ?",
                [$facilityId, "{$year}%"]
            );
            
            $nextNumber = 1;
            if ($lastNumber && $lastNumber['last']) {
                $parts = explode('-', $lastNumber['last']);
                $nextNumber = (int)end($parts) + 1;
            }
            
            $internalNumber = sprintf("%s-%06d", $year, $nextNumber);
            
            // Insérer le dossier patient
            $patientData = [
                'facility_id' => $facilityId,
                'npi' => $data['npi'],
                'internal_number' => $internalNumber,
                'patient_code' => Security::hashPassword($patientCode),
                'nom' => strtoupper($data['nom']),
                'prenom' => ucfirst($data['prenom']),
                'sexe' => $data['sexe'],
                'date_naissance' => $data['date_naissance'] ?? null,
                'age_approximatif' => $data['age_approximatif'] ?? null,
                'lieu_naissance' => $data['lieu_naissance'] ?? null,
                'nationalite' => $data['nationalite'] ?? 'Béninoise',
                'telephone' => $data['telephone'] ?? null,
                'email' => $data['email'] ?? null,
                'adresse' => $data['adresse'] ?? null,
                'quartier' => $data['quartier'] ?? null,
                'ville' => $data['ville'] ?? null,
                'contact_urgence_nom' => $data['contact_urgence_nom'] ?? null,
                'contact_urgence_telephone' => $data['contact_urgence_telephone'] ?? null,
                'contact_urgence_parente' => $data['contact_urgence_parente'] ?? null,
                'groupe_sanguin' => $data['groupe_sanguin'] ?? null,
                'allergies' => $data['allergies'] ?? null,
                'antecedents' => $data['antecedents'] ?? null,
                'photo_path' => $data['photo_path'] ?? null,
                'qr_code_data' => null, // Sera généré après
                'medecin_traitant_id' => $data['medecin_traitant_id'] ?? null,
                'statut' => 'actif',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $patientId = $this->db->insert('patient_files', $patientData);
            
            // Générer les données QR Code
            $patient = $this->findById($patientId);
            $qrData = Security::generatePatientQRData($patient);
            
            $this->db->update('patient_files', ['qr_code_data' => $qrData], 'id = ?', [$patientId]);
            
            // Log création
            $this->auditLogger->log('PATIENT_CREATED', 'patient_file', $patientId, "NPI: {$data['npi']}");
            
            $this->db->commit();
            
            // Retourner avec le code PIN en clair (à remettre au patient)
            return $patientId;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Crée un dossier patient inconnu (urgence sans identification)
     */
    public function createUnknown(int $facilityId, array $data = []): int
    {
        $year = date('Y');
        
        // Générer NPI temporaire
        $randomPart = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $npiTemp = "INCONNU-{$year}-{$randomPart}";
        
        $unknownData = array_merge([
            'nom' => 'INCONNU',
            'prenom' => 'Patient',
            'sexe' => 'M',
            'age_approximatif' => 30,
            'description_physique' => 'Non renseignée',
            'photo_path' => null
        ], $data);
        
        $unknownData['npi'] = $npiTemp;
        
        return $this->create($unknownData, $facilityId);
    }

    /**
     * Vérifie le code d'accès patient
     */
    public function verifyPatientCode(int $patientFileId, string $code): bool
    {
        $patient = $this->findById($patientFileId);
        
        if (!$patient) {
            return false;
        }
        
        return Security::verifyPassword($code, $patient['patient_code']);
    }

    /**
     * Met à jour un dossier patient
     */
    public function update(int $id, array $data): bool
    {
        $oldPatient = $this->findById($id);
        
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $affected = $this->db->update('patient_files', $data, 'id = ?', [$id]);
        
        if ($affected > 0) {
            $this->auditLogger->logSensitiveChange('patient_file', $id, 'dossier', $oldPatient, $data);
        }
        
        return $affected > 0;
    }

    /**
     * Fusionne un patient inconnu avec un vrai NPI
     */
    public function mergeUnknownWithReal(int $unknownId, string $realNPI): bool
    {
        $this->db->beginTransaction();
        
        try {
            $unknown = $this->findById($unknownId);
            $real = $this->findByNPI($realNPI);
            
            if (!$unknown || !$real) {
                throw new \Exception("Patient non trouvé");
            }
            
            if ($unknown['npi'] === $realNPI) {
                throw new \Exception("Déjà fusionné");
            }
            
            // Transférer toutes les consultations vers le vrai dossier
            $this->db->query(
                "UPDATE consultations SET patient_file_id = ? WHERE patient_file_id = ?",
                [$real['id'], $unknownId]
            );
            
            // Transférer les hospitalisations
            $this->db->query(
                "UPDATE hospitalizations SET patient_file_id = ? WHERE patient_file_id = ?",
                [$real['id'], $unknownId]
            );
            
            // Marquer comme fusionné
            $this->update($unknownId, [
                'merged_into_id' => $real['id'],
                'statut' => 'fusionné',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $this->auditLogger->log('PATIENT_MERGED', 'patient_file', $unknownId, "Fusionné avec NPI: {$realNPI}");
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Recherche des patients
     */
    public function search(
        ?string $query = null,
        ?string $npi = null,
        ?int $facilityId = null,
        int $limit = 50
    ): array {
        $conditions = [];
        $params = [];
        
        $conditions[] = "pf.deleted_at IS NULL";
        
        if ($query) {
            $conditions[] = "(pf.nom LIKE ? OR pf.prenom LIKE ? OR pf.internal_number LIKE ?)";
            $params[] = "%{$query}%";
            $params[] = "%{$query}%";
            $params[] = "%{$query}%";
        }
        
        if ($npi) {
            $conditions[] = "pf.npi = ?";
            $params[] = $npi;
        }
        
        if ($facilityId) {
            $conditions[] = "pf.facility_id = ?";
            $params[] = $facilityId;
        }
        
        $where = implode(' AND ', $conditions);
        
        $sql = "SELECT pf.*, 
                       u.nom as medecin_nom, u.prenom as medecin_prenom
                FROM patient_files pf
                LEFT JOIN users u ON pf.medecin_traitant_id = u.id
                WHERE {$where}
                ORDER BY pf.created_at DESC
                LIMIT ?";
        
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Récupère l'historique médical d'un patient
     */
    public function getMedicalHistory(int $patientFileId): array
    {
        $history = [];
        
        // Consultations
        $history['consultations'] = $this->db->fetchAll(
            "SELECT c.*, u.nom as medecin_nom, u.prenom as medecin_prenom
             FROM consultations c
             INNER JOIN users u ON c.user_id = u.id
             WHERE c.patient_file_id = ?
             ORDER BY c.created_at DESC
             LIMIT 100",
            [$patientFileId]
        );
        
        // Hospitalisations
        $history['hospitalizations'] = $this->db->fetchAll(
            "SELECT h.*, s.nom as service_nom
             FROM hospitalizations h
             INNER JOIN services s ON h.service_id = s.id
             WHERE h.patient_file_id = ?
             ORDER BY h.admission_date DESC
             LIMIT 100",
            [$patientFileId]
        );
        
        // Prescriptions
        $history['prescriptions'] = $this->db->fetchAll(
            "SELECT p.*, c.created_at as consultation_date
             FROM prescriptions p
             INNER JOIN consultations c ON p.consultation_id = c.id
             WHERE c.patient_file_id = ?
             ORDER BY c.created_at DESC
             LIMIT 100",
            [$patientFileId]
        );
        
        // Résultats labo
        $history['lab_results'] = $this->db->fetchAll(
            "SELECT lr.*, u.nom as biologiste_nom
             FROM lab_requests lr
             INNER JOIN users u ON lr.validated_by = u.id
             WHERE lr.patient_file_id = ? AND lr.status = 'validé'
             ORDER BY lr.created_at DESC
             LIMIT 100",
            [$patientFileId]
        );
        
        return $history;
    }

    /**
     * Désigne un médecin traitant
     */
    public function setMedecinTraitant(int $patientFileId, int $medecinId): bool
    {
        $result = $this->update($patientFileId, ['medecin_traitant_id' => $medecinId]);
        
        if ($result) {
            $this->auditLogger->log('MEDECIN_TRAITANT_SET', 'patient_file', $patientFileId, "Médecin ID: {$medecinId}");
        }
        
        return $result;
    }

    /**
     * Récupère les statistiques patients
     */
    public function getStats(int $facilityId, ?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = ["facility_id = ?"];
        $params = [$facilityId];
        
        if ($startDate) {
            $conditions[] = "created_at >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $conditions[] = "created_at <= ?";
            $params[] = $endDate;
        }
        
        $where = implode(' AND ', $conditions);
        
        return $this->db->fetch(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN sexe = 'M' THEN 1 ELSE 0 END) as hommes,
                SUM(CASE WHEN sexe = 'F' THEN 1 ELSE 0 END) as femmes,
                SUM(CASE WHEN npi LIKE 'INCONNU%' THEN 1 ELSE 0 END) as inconnus,
                SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as actifs
             FROM patient_files
             WHERE {$where}",
            $params
        );
    }

    /**
     * Vérifie si un NPI existe déjà
     */
    public function npiExists(string $npi): bool
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM patient_files WHERE npi = ?",
            [$npi]
        );
        
        return $result['count'] > 0;
    }
}
