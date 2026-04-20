<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IlaraNet Bénin | Plateforme Numérique de Gestion des Centres de Santé</title>
    <meta name="description" content="Écosystème numérique complet pour hôpitaux, cliniques et pharmacies au Bénin. Conformité ANIP & OHADA.">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9', // Bleu ciel médical
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        },
                        accent: {
                            500: '#10b981', // Vert santé
                            600: '#059669',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .hero-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#0ea5e9 0.5px, transparent 0.5px), radial-gradient(#0ea5e9 0.5px, #f8fafc 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="font-sans text-slate-800 antialiased bg-slate-50">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-card shadow-sm transition-all duration-300" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg">
                        I
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">IlaraNet <span class="text-brand-600">Bénin</span></h1>
                        <p class="text-xs text-slate-500 font-medium">République du Bénin — 2026</p>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#solutions" class="text-slate-600 hover:text-brand-600 font-medium transition">Solutions</a>
                    <a href="#fonctionnalites" class="text-slate-600 hover:text-brand-600 font-medium transition">Fonctionnalités</a>
                    <a href="#conformite" class="text-slate-600 hover:text-brand-600 font-medium transition">Conformité</a>
                    <a href="#tarifs" class="text-slate-600 hover:text-brand-600 font-medium transition">Tarifs</a>
                </div>

                <div class="flex items-center gap-4">
                    <a href="/login" class="text-slate-600 hover:text-brand-600 font-medium hidden sm:block">Se connecter</a>
                    <a href="/register" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2.5 rounded-full font-semibold shadow-lg shadow-brand-500/30 transition transform hover:-translate-y-0.5">
                        Demander une démo
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-sm font-semibold mb-8 animate-pulse">
                <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                Nouvelle Version 2026 Disponible
            </div>
            
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                La Santé Numérique <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-accent-500">Unifiée au Bénin</span>
            </h1>
            
            <p class="text-xl text-slate-600 max-w-3xl mx-auto mb-10 leading-relaxed">
                Écosystème complet pour Hôpitaux, Cliniques et Pharmacies. 
                Digitalisez le parcours patient, sécurisez les données et conformez-vous aux normes <strong>ANIP & OHADA</strong>.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/register" class="bg-brand-600 hover:bg-brand-700 text-white text-lg px-8 py-4 rounded-xl font-bold shadow-xl shadow-brand-500/20 transition transform hover:-translate-y-1">
                    Commencer l'inscription
                </a>
                <a href="#demo" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-lg px-8 py-4 rounded-xl font-bold shadow-md transition flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Voir la démo vidéo
                </a>
            </div>

            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 opacity-70 grayscale hover:grayscale-0 transition duration-500">
                <!-- Logos Partenaires (Simulés) -->
                <div class="flex items-center justify-center gap-2"><span class="font-bold text-xl">ANIP</span></div>
                <div class="flex items-center justify-center gap-2"><span class="font-bold text-xl">CNSS</span></div>
                <div class="flex items-center justify-center gap-2"><span class="font-bold text-xl">OMS CIM-11</span></div>
                <div class="flex items-center justify-center gap-2"><span class="font-bold text-xl">OHADA</span></div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-slate-100">
                <div class="p-4">
                    <div class="text-4xl font-bold text-brand-600 mb-2">2 Plateformes</div>
                    <div class="text-slate-600">Interconnectées par API Sécurisée</div>
                </div>
                <div class="p-4">
                    <div class="text-4xl font-bold text-accent-500 mb-2">10+ Modules</div>
                    <div class="text-slate-600">Médicaux & Administratifs Intégrés</div>
                </div>
                <div class="p-4">
                    <div class="text-4xl font-bold text-slate-800 mb-2">100% Numérique</div>
                    <div class="text-slate-600">Conformité Réglementaire Totale</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="solutions" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base text-brand-600 font-semibold tracking-wide uppercase">Solutions Complètes</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Tout ce dont votre établissement a besoin
                </p>
                <p class="mt-4 max-w-2xl text-xl text-slate-500 mx-auto">
                    Deux écosystèmes distincts mais parfaitement synchronisés pour une continuité des soins absolue.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition duration-300 border border-slate-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Gestion Hospitalière</h3>
                    <p class="text-slate-600 mb-4">Urgences (Triage ESI), Hospitalisation, Bloc Opératoire, Maternité et Laboratoire.</p>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Dossiers Patients Sécurisés</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Triage Intelligent</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Suivi Lit en Temps Réel</li>
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition duration-300 border border-slate-100">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Réseau Pharmacies</h3>
                    <p class="text-slate-600 mb-4">Matching intelligent ordonnance-pharmacie, gestion de stock et réseau national connecté.</p>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Vérification Token QR</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Disponibilité Cross-Réseau</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Alertes Ruptures Nationales</li>
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition duration-300 border border-slate-100">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Admin & Finance</h3>
                    <p class="text-slate-600 mb-4">Comptabilité OHADA, Facturation, RH/CNSS et Tableaux de bord décisionnels.</p>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Conformité OHADA</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Paiements Mobile (MoMo/Flooz)</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Audit Trail Immuable</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Security & Compliance -->
    <section id="conformite" class="py-24 bg-brand-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pattern-dots"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-extrabold mb-6">Sécurité de Niveau Bancaire</h2>
                    <p class="text-brand-100 text-lg mb-8">
                        Vos données médicales sont protégées par les standards les plus stricts. Conformité totale avec la législation béninoise.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand-800 flex items-center justify-center border border-brand-700">
                                <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold">Chiffrement AES-256</h4>
                                <p class="text-brand-200 mt-1">Données chiffrées au repos et en transit (TLS 1.3).</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand-800 flex items-center justify-center border border-brand-700">
                                <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold">Conformité ANIP & OHADA</h4>
                                <p class="text-brand-200 mt-1">Vérification NPI temps réel et comptabilité certifiée.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand-800 flex items-center justify-center border border-brand-700">
                                <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold">Audit Trail Immuable</h4>
                                <p class="text-brand-200 mt-1">Traçabilité complète de chaque action (Qui, Quoi, Quand).</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-12 lg:mt-0 relative">
                    <div class="bg-brand-800 rounded-2xl p-8 border border-brand-700 shadow-2xl">
                        <div class="flex items-center justify-between mb-6 border-b border-brand-700 pb-4">
                            <span class="text-sm font-mono text-brand-300">STATUS: SECURE</span>
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                        </div>
                        <div class="space-y-4 font-mono text-sm text-brand-200">
                            <div class="flex justify-between"><span>Encryption:</span> <span class="text-white">AES-256-GCM</span></div>
                            <div class="flex justify-between"><span>Protocol:</span> <span class="text-white">TLS 1.3</span></div>
                            <div class="flex justify-between"><span>Auth:</span> <span class="text-white">2FA + RBAC</span></div>
                            <div class="flex justify-between"><span>Backup:</span> <span class="text-white">Auto (J+1)</span></div>
                            <div class="mt-4 pt-4 border-t border-brand-700 text-xs text-brand-400">
                                Dernière vérification de sécurité: Il y a 2 minutes
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="tarifs" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-slate-900">Des tarifs adaptés à votre structure</h2>
                <p class="mt-4 text-xl text-slate-500">Sans frais cachés. Annulable à tout moment.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Plan Cabinet -->
                <div class="border border-slate-200 rounded-2xl p-8 hover:border-brand-500 transition relative">
                    <h3 class="text-lg font-semibold text-slate-900">Plan Cabinet</h3>
                    <div class="my-4">
                        <span class="text-4xl font-bold text-slate-900">25k</span>
                        <span class="text-slate-500">FCFA/mois</span>
                    </div>
                    <p class="text-slate-500 text-sm mb-6">Idéal pour cabinets médicaux et petits centres.</p>
                    <ul class="space-y-3 mb-8 text-sm text-slate-600">
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Jusqu'à 3 médecins</li>
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Dossiers patients illimités</li>
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Ordonnances numériques</li>
                    </ul>
                    <a href="/register?plan=cabinet" class="block w-full py-3 px-4 bg-brand-50 text-brand-700 font-bold text-center rounded-lg hover:bg-brand-100 transition">Choisir ce plan</a>
                </div>

                <!-- Plan Clinique (Featured) -->
                <div class="border-2 border-brand-500 rounded-2xl p-8 relative shadow-xl transform scale-105 bg-white z-10">
                    <div class="absolute top-0 right-0 bg-brand-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">POPULAIRE</div>
                    <h3 class="text-lg font-semibold text-slate-900">Plan Clinique</h3>
                    <div class="my-4">
                        <span class="text-4xl font-bold text-slate-900">75k</span>
                        <span class="text-slate-500">FCFA/mois</span>
                    </div>
                    <p class="text-slate-500 text-sm mb-6">Pour cliniques privées et centres de santé.</p>
                    <ul class="space-y-3 mb-8 text-sm text-slate-600">
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Jusqu'à 20 médecins</li>
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Modules Labo & Imagerie</li>
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Facturation & Caisse</li>
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Support prioritaire</li>
                    </ul>
                    <a href="/register?plan=clinique" class="block w-full py-3 px-4 bg-brand-600 text-white font-bold text-center rounded-lg hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">Commencer l'essai gratuit</a>
                </div>

                <!-- Plan Hôpital -->
                <div class="border border-slate-200 rounded-2xl p-8 hover:border-brand-500 transition">
                    <h3 class="text-lg font-semibold text-slate-900">Plan Hôpital</h3>
                    <div class="my-4">
                        <span class="text-4xl font-bold text-slate-900">Sur Devis</span>
                    </div>
                    <p class="text-slate-500 text-sm mb-6">Solution complète pour grands établissements.</p>
                    <ul class="space-y-3 mb-8 text-sm text-slate-600">
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Tous modules inclus</li>
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Urgences & Hospitalisation</li>
                        <li class="flex gap-2"><span class="text-green-500">✓</span> API Personnalisée</li>
                        <li class="flex gap-2"><span class="text-green-500">✓</span> Formation équipe</li>
                    </ul>
                    <a href="/contact" class="block w-full py-3 px-4 bg-white border border-slate-300 text-slate-700 font-bold text-center rounded-lg hover:bg-slate-50 transition">Contacter les ventes</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-white text-lg font-bold mb-4">IlaraNet Bénin</h3>
                    <p class="text-sm text-slate-400 max-w-sm">
                        Plateforme nationale de gestion des centres de santé. Connecter les soins, sécuriser les données, améliorer la santé au Bénin.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Liens Rapides</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">À propos</a></li>
                        <li><a href="#" class="hover:text-white transition">Sécurité</a></li>
                        <li><a href="#" class="hover:text-white transition">API Documentation</a></li>
                        <li><a href="#" class="hover:text-white transition">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Légal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Mentions Légales</a></li>
                        <li><a href="#" class="hover:text-white transition">Politique de Confidentialité</a></li>
                        <li><a href="#" class="hover:text-white transition">CGU</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; 2026 IlaraNet Bénin. Tous droits réservés.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <span>Fait avec ❤️ à Cotonou</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
