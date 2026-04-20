<?php
/**
 * Page d'inscription - IlaraNet Bénin
 * Inscription des établissements de santé
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Établissement - IlaraNet Bénin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden" x-data="registrationForm()">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-green-600 px-8 py-6">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                    <span class="text-2xl font-bold text-blue-600">I</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">IlaraNet Bénin</h1>
                    <p class="text-blue-100 text-sm">Inscription de votre établissement de santé</p>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="submitRegistration" class="p-8">
            <!-- Informations Établissement -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Informations de l'établissement
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom de l'établissement *</label>
                        <input type="text" x-model="form.name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Ex: Clinique Saint Michel">
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type d'établissement *</label>
                        <select x-model="form.type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">Sélectionner...</option>
                            <option value="hopital">Hôpital</option>
                            <option value="clinique">Clinique Privée</option>
                            <option value="cabinet">Cabinet Médical</option>
                            <option value="polyclinique">Polyclinique</option>
                            <option value="centre_sante">Centre de Santé</option>
                        </select>
                    </div>

                    <!-- RCCM -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Numéro RCCM *</label>
                        <input type="text" x-model="form.rccm" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Ex: RCCM/ABJ/2024/B/12345">
                    </div>

                    <!-- Autorisation Sanitaire -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Autorisation Sanitaire *</label>
                        <input type="text" x-model="form.health_permit" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="N° d'autorisation du Ministère">
                    </div>

                    <!-- Adresse -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adresse complète *</label>
                        <input type="text" x-model="form.address" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Quartier, Commune, Ville">
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone principal *</label>
                        <input type="tel" x-model="form.phone" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="+229 XX XX XX XX">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email officiel *</label>
                        <input type="email" x-model="form.email" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="contact@etablissement.bj">
                    </div>
                </div>
            </div>

            <!-- Informations Directeur -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Informations du Directeur / Responsable
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                        <input type="text" x-model="director.name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone personnel *</label>
                        <input type="tel" x-model="director.phone" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="+229 XX XX XX XX">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email personnel *</label>
                        <input type="email" x-model="director.email" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe *</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" x-model="director.password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Logo de l'établissement
                </h2>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer"
                    @click="$refs.logoInput.click()"
                    @dragover.prevent @drop.prevent="handleDrop">
                    <input type="file" x-ref="logoInput" @change="handleFileSelect" accept="image/*" class="hidden">
                    
                    <template x-if="!logoPreview">
                        <div>
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Glissez-déposez votre logo ou cliquez pour parcourir</p>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG jusqu'à 2MB</p>
                        </div>
                    </template>
                    
                    <template x-if="logoPreview">
                        <div class="relative inline-block">
                            <img :src="logoPreview" alt="Aperçu logo" class="h-32 w-auto mx-auto rounded-lg">
                            <button type="button" @click.stop="removeLogo"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Conditions -->
            <div class="mb-8">
                <label class="flex items-start space-x-3">
                    <input type="checkbox" x-model="form.acceptTerms" required
                        class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-600">
                        J'accepte les <a href="#" class="text-blue-600 hover:underline">conditions générales d'utilisation</a> 
                        et la <a href="#" class="text-blue-600 hover:underline">politique de confidentialité</a> d'IlaraNet Bénin.
                        Je certifie que toutes les informations fournies sont exactes et conformes à la réglementation béninoise.
                    </span>
                </label>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between">
                <a href="/login" class="text-blue-600 hover:text-blue-700 font-medium">
                    ← Déjà inscrit ? Se connecter
                </a>
                
                <button type="submit" 
                    :disabled="submitting || !form.acceptTerms"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-green-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition transform hover:scale-105">
                    <span x-show="!submitting">Soumettre la demande d'inscription</span>
                    <span x-show="submitting">
                        <svg class="animate-spin inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Envoi en cours...
                    </span>
                </button>
            </div>
        </form>

        <!-- Info Box -->
        <div class="bg-blue-50 px-8 py-4 border-t border-blue-100">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <strong>Information importante :</strong> Votre demande sera examinée par notre équipe sous 24-48h. 
                    Vous recevrez un email de confirmation une fois votre établissement validé par le Super Admin.
                    Conformément à la réglementation béninoise, nous vérifierons votre numéro RCCM et autorisation sanitaire.
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="notification.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed top-4 right-4 max-w-md w-full z-50">
        <div :class="notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'" 
            class="rounded-lg shadow-lg p-4 text-white">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="notification.type === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path x-show="notification.type === 'error'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p x-text="notification.message"></p>
            </div>
        </div>
    </div>

    <script>
        function registrationForm() {
            return {
                showPassword: false,
                submitting: false,
                logoFile: null,
                logoPreview: null,
                notification: {
                    show: false,
                    type: 'success',
                    message: ''
                },
                form: {
                    name: '',
                    type: '',
                    rccm: '',
                    health_permit: '',
                    address: '',
                    phone: '',
                    email: '',
                    acceptTerms: false
                },
                director: {
                    name: '',
                    phone: '',
                    email: '',
                    password: ''
                },
                
                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.validateAndPreviewLogo(file);
                    }
                },
                
                handleDrop(event) {
                    const file = event.dataTransfer.files[0];
                    if (file) {
                        this.validateAndPreviewLogo(file);
                    }
                },
                
                validateAndPreviewLogo(file) {
                    if (!file.type.startsWith('image/')) {
                        this.showNotification('Seuls les fichiers image sont acceptés', 'error');
                        return;
                    }
                    
                    if (file.size > 2 * 1024 * 1024) {
                        this.showNotification('Le fichier ne doit pas dépasser 2MB', 'error');
                        return;
                    }
                    
                    this.logoFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.logoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },
                
                removeLogo() {
                    this.logoFile = null;
                    this.logoPreview = null;
                    this.$refs.logoInput.value = '';
                },
                
                async submitRegistration() {
                    this.submitting = true;
                    
                    // Simulation d'envoi (à remplacer par un vrai appel API)
                    await new Promise(resolve => setTimeout(resolve, 2000));
                    
                    // Ici, vous feriez un fetch POST vers /api/register
                    console.log('Données à envoyer:', {
                        facility: this.form,
                        director: this.director,
                        logo: this.logoFile
                    });
                    
                    this.submitting = false;
                    this.showNotification(
                        'Demande d\'inscription soumise avec succès ! Vous recevrez un email de confirmation sous 24-48h.',
                        'success'
                    );
                    
                    // Reset partiel
                    this.form.acceptTerms = false;
                },
                
                showNotification(message, type) {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    
                    setTimeout(() => {
                        this.notification.show = false;
                    }, 5000);
                }
            }
        }
    </script>
</body>
</html>
