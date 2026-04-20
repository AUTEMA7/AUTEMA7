# IlaraNet Bénin — Base applicative (PHP + Tailwind + Alpine + Arcane.php + MySQL)

Cette version ajoute les fondations demandées :

- RBAC 3 niveaux (Direction / Chef / Personnel)
- 2FA OTP SMS (simulation locale)
- Modules: Urgences, Hospitalisation, Laboratoire, Pharmacie API
- Journal d'audit immuable (hash chain + horodatage WAT)
- Intégrations ANIP NPI + paiements mobiles (MoMo/Flooz/C-Cash) en mode simulation

## Stack

- Backend: PHP (routing Arcane)
- Frontend: Tailwind CSS + Alpine.js
- Base de données: MySQL

## Installation

```bash
cp .env.example .env
mysql -u root -p < database/schema.sql
php -S 127.0.0.1:8000 -t public
```

## Comptes de démo (mot de passe: `password`)

- Directeur: `directeur@ilaranet.bj`
- Chef de service: `chef@ilaranet.bj`
- Personnel: `staff@ilaranet.bj`
- Code service (chef/personnel): `123456`

## OTP SMS

Les OTP sont simulés et écrits dans `storage_sms.log`.

## Routes principales

- Auth: `/`, `/login`, `/otp`, `/service-access`, `/logout`
- Dashboard: `/dashboard`
- Patients + ANIP: `/patients`, `/integrations/anip/verify`
- Paiements mobile: `/payments/mobile`
- Modules: `/modules/emergency`, `/modules/hospitalization`, `/modules/lab`, `/modules/pharmacy`

## Notes importantes

- Pages système ajoutées: erreurs `403` et `404` pour éviter les écrans blancs en cas de route manquante ou accès interdit.
- Les intégrations externes (ANIP réel, MoMo/Flooz/C-Cash réel, API pharmacie nationale) sont exposées ici en **simulation** pour permettre le développement sans dépendances réseau externes.
- Le journal d'audit est append-only côté application, avec empreinte `hash_chain` stockée dans la table `audit_logs`.
