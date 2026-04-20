<?php
/**
 * Vue de vérification 2FA - IlaraNet Bénin
 */
?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8 fade-in">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-gradient-to-r from-benin-yellow to-benin-red rounded-full flex items-center justify-center">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="mt-4 text-3xl font-bold text-gray-900">Vérification 2FA</h1>
            <p class="mt-2 text-sm text-gray-600">Entrez le code reçu par SMS</p>
        </div>

        <!-- Formulaire OTP -->
        <form class="mt-8 space-y-6" action="/login/2fa" method="POST" x-data="{ otp: '' }">
            <?= csrf_field() ?>
            
            <div class="space-y-4">
                <!-- Champ OTP -->
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                        Code à 6 chiffres
                    </label>
                    <input id="otp" 
                           name="otp" 
                           type="text" 
                           inputmode="numeric"
                           pattern="[0-9]*"
                           autocomplete="one-time-code"
                           x-model="otp"
                           @input="otp = otp.replace(/[^0-9]/g, '').slice(0, 6)"
                           required 
                           autofocus
                           placeholder="000000"
                           class="appearance-none block w-full px-3 py-4 text-3xl text-center tracking-[1em] border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition font-mono">
                </div>

                <!-- Timer -->
                <div class="text-center" x-data="{ seconds: 30 }" x-init="setInterval(() => { if (seconds > 0) seconds-- }, 1000)">
                    <p class="text-sm text-gray-600">
                        Renvoi du code dans <span x-text="seconds" class="font-bold text-primary-600"></span>s
                    </p>
                    <button type="button" 
                            x-show="seconds === 0"
                            class="mt-2 text-sm font-medium text-primary-600 hover:text-primary-500">
                        Renvoyer le code
                    </button>
                </div>
            </div>

            <!-- Bouton de soumission -->
            <div>
                <button type="submit" 
                        :disabled="otp.length !== 6"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-benin-green to-primary-600 hover:from-benin-green hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-white opacity-75 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    Vérifier
                </button>
            </div>

            <!-- Retour -->
            <div class="text-center">
                <a href="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    ← Retour à la connexion
                </a>
            </div>
        </form>

        <!-- Info sécurité -->
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-xs text-blue-700">
                    <p class="font-semibold mb-1">Pourquoi ce code ?</p>
                    <p>La double authentification (2FA) protège votre compte et garantit la sécurité des données médicales conformément à l'ANIP.</p>
                </div>
            </div>
        </div>
    </div>
</div>
