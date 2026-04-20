<?php
/**
 * Nouveau Patient - IlaraNet Bénin
 */

$pageTitle = 'Nouveau Patient';
ob_start();
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Inscription Patient</h1>
            <p class="text-gray-600">Créer un nouveau dossier patient avec NPI ANIP</p>
        </div>
        <a href="/patients" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour</span>
        </a>
    </div>

    <!-- Form -->
    <form action="/patients" method="POST" class="space-y-6" x-data="{ 
        npiType: 'normal',
        showPin: false,
        generatedPin: '',
        async verifyNPI() {
            // Appel API ANIP pour vérifier le NPI
        }
    }">
        <?= csrf_field() ?>
        
        <!-- Identification Section -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">🆔</span>
                Identification
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type d'identification *</label>
                    <select x-model="npiType" name="npi_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="normal">NPI ANIP (Normal)</option>
                        <option value="temporaire">NPI Temporaire (Urgence/Inconnu)</option>
                    </select>
                </div>
                
                <div x-show="npiType === 'normal'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro NPI ANIP *</label>
                    <div class="flex space-x-2">
                        <input type="text" name="npi" placeholder="Ex: BJ2025001234X" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 uppercase font-mono" pattern="[A-Z]{2}[0-9]{10}[A-Z]">
                        <button type="button" @click="verifyNPI()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Vérifier</button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Format: 2 lettres + 10 chiffres + 1 lettre</p>
                </div>
                
                <div x-show="npiType === 'temporaire'" class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif NPI temporaire *</label>
                    <select name="temp_reason" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Sélectionner...</option>
                        <option value="urgence">Urgence vitale</option>
                        <option value="inconscient">Patient inconscient</option>
                        <option value="sans_papiers">Sans papiers d'identité</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Informations Personnelles -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-2">👤</span>
                Informations Personnelles
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" name="last_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 uppercase" placeholder="NOM">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénoms *</label>
                    <input type="text" name="first_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Prénoms">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance *</label>
                    <input type="date" name="birth_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sexe *</label>
                    <select name="gender" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Sélectionner...</option>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lieu de naissance</label>
                    <input type="text" name="birth_place" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Ville, Commune">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nationalité</label>
                    <input type="text" name="nationality" value="Béninoise" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>
        </div>

        <!-- Coordonnées -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-2">📞</span>
                Coordonnées & Contact
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input type="tel" name="phone" required class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="+229 XX XX XX XX" pattern="\+229[0-9]{8}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="email@exemple.com">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse complète</label>
                    <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Quartier, Rue, Numéro..."></textarea>
                </div>
            </div>
        </div>

        <!-- Contacts d'Urgence -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-2">🚨</span>
                Contacts d'Urgence
            </h2>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom contact 1</label>
                        <input type="text" name="emergency_contact_1_name" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parenté</label>
                        <select name="emergency_contact_1_relation" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Sélectionner...</option>
                            <option value="conjoint">Conjoint(e)</option>
                            <option value="parent">Parent</option>
                            <option value="enfant">Enfant</option>
                            <option value="frere_soeur">Frère/Sœur</option>
                            <option value="ami">Ami</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                        <input type="tel" name="emergency_contact_1_phone" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
        </div>

        <!-- Assurance -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-2">🏥</span>
                Couverture Médicale
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type d'assurance *</label>
                    <select name="insurance_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Sélectionner...</option>
                        <option value="cnss">CNSS</option>
                        <option value="crss">CRSS (Fonctionnaires)</option>
                        <option value="privee">Assurance Privée</option>
                        <option value="mutuelle">Mutuelle</option>
                        <option value="aucune">Aucune</option>
                    </select>
                </div>
                
                <div id="insurance_number_field">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro d'assuré</label>
                    <input type="text" name="insurance_number" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Numéro d'immatriculation">
                </div>
            </div>
        </div>

        <!-- Photo & Signature -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-2">📷</span>
                Photo & Code d'Accès
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo du patient *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors cursor-pointer">
                        <input type="file" name="photo" accept="image/*" class="hidden" id="photo_input">
                        <label for="photo_input" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Cliquez pour prendre une photo ou importer</p>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code d'accès patient (PIN)</label>
                    <div class="flex items-center space-x-2">
                        <div class="flex-1 relative">
                            <input :type="showPin ? 'text' : 'password'" name="pin_code" readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono text-lg text-center tracking-widest" value="<?= strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)) ?>">
                        </div>
                        <button type="button" @click="showPin = !showPin" class="px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <button type="button" @click="$event.target.previousElementSibling.previousElementSibling.value = Math.random().toString(36).substring(2,8).toUpperCase()" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Code à 6-8 chiffres remis au patient. Nécessaire pour accéder au dossier médical.</p>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6">
            <a href="/patients" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">Annuler</a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Créer le dossier patient</span>
            </button>
        </div>
    </form>
</div>

<script>
document.querySelector('select[name="insurance_type"]').addEventListener('change', function() {
    const field = document.getElementById('insurance_number_field');
    if (this.value === 'aucune') {
        field.classList.add('opacity-50');
        field.querySelector('input').disabled = true;
    } else {
        field.classList.remove('opacity-50');
        field.querySelector('input').disabled = false;
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
