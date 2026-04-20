# IlaraNet Bénin — MVP (PHP + Tailwind + Alpine + Arcane.php + MySQL)

Ce dépôt contient une base fonctionnelle pour démarrer l'application IlaraNet Bénin avec :

- **PHP** (backend)
- **Arcane.php** (micro-noyau de routing maison `src/Arcane.php`)
- **MySQL** (persistance)
- **Tailwind CSS** (UI)
- **Alpine.js** (interactions frontend)

## Fonctionnalités incluses (MVP)

- Page de connexion (compte démo)
- Tableau de bord directeur
- Enregistrement de patients (NPI, nom, téléphone)
- Liste des patients

## Lancer le projet

1. Copier l'environnement :
   ```bash
   cp .env.example .env
   ```
2. Créer la base :
   ```bash
   mysql -u root -p < database/schema.sql
   ```
3. Lancer le serveur PHP :
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
4. Ouvrir : `http://127.0.0.1:8000`

### Compte démo

- **Email**: `admin@ilaranet.bj`
- **Mot de passe**: `admin123`

## Prochaines étapes recommandées

- Ajouter RBAC 3 niveaux (Direction / Chef / Personnel)
- Ajouter 2FA OTP SMS
- Modules Urgence, Hospitalisation, Laboratoire, Pharmacie API
- Journal d'audit immuable
- Intégration ANIP NPI et paiements mobiles (MoMo/Flooz/C-Cash)
