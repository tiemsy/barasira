# Spécifications techniques — Barasira

Version 1.0 — 30 juillet 2026

## 1. Objet

Barasira est une marketplace de services mettant en relation des clients et des
prestataires au Mali. Le produit couvre l’inscription, la vérification des
profils, la publication de missions, les candidatures, la messagerie, les
paiements, les évaluations, les partenaires et l’administration.

Ce document décrit l’état effectivement implémenté dans le dépôt. Le contrat
détaillé de l’API REST est disponible dans `storage/api-docs/api-docs.yaml` et
dans Swagger UI à l’adresse `/api/documentation`.

## 2. Architecture

### 2.1 Composants

| Couche | Technologie | Responsabilité |
|---|---|---|
| Reverse proxy applicatif | Nginx | HTTP, fichiers statiques, transfert vers PHP-FPM |
| Backend | PHP 8.2+ / Laravel 12 | API, pages Inertia, règles métier et tâches |
| Frontend | Vue 3 / Inertia.js | interface publique, espaces utilisateur et administration |
| Build frontend | Vite 5 / Sass | compilation JS, Vue, styles et images |
| Base de données | MySQL 8 | données métier et contraintes relationnelles |
| Cache et files | Redis 7 | cache applicatif et files de notifications |
| Authentification | Sanctum / session | session web et jeton Bearer facultatif pour client natif |
| Documentation API | L5-Swagger / OpenAPI 3 | contrat et interface interactive |
| Supervision processus | Supervisor | maintien de Nginx et PHP-FPM dans l’image |

### 2.2 Organisation du code

- `app/Http/Controllers/Api` : endpoints JSON ;
- `app/Http/Controllers/Front` : pages et actions web/Inertia ;
- `app/Http/Controllers/Admin` : administration et superadministration ;
- `app/Http/Requests` : validation et normalisation des entrées ;
- `app/Policies` et services métier : autorisations et règles transversales ;
- `app/Repositories` : accès aux données réutilisable ;
- `app/Notifications` et `app/Channels` : notifications internes et externes ;
- `resources/js` : application Vue ;
- `resources/scss` : styles et tokens de design ;
- `routes/web.php`, `routes/api.php`, `routes/admin.php` : exposition HTTP ;
- `database/migrations` : schéma versionné ;
- `app/Swagger` : source des artefacts OpenAPI.

### 2.3 Flux HTTP

```text
Navigateur / application mobile
              |
              v
       Proxy HTTPS Coolify
              |
              v
      conteneur app:80 (Nginx)
              |
              v
           PHP-FPM
              |
       +------+------+
       |             |
     MySQL         Redis
```

Le proxy Coolify doit cibler le service applicatif sur son port interne `80`.
Les ports `80` et `443` ne doivent pas être publiés par le Compose : ils sont
déjà occupés par le proxy de la plateforme.

## 3. Environnements

### 3.1 Développement

- target Docker : `development` ;
- Compose : `docker-compose.yml` avec surcharge
  `docker-compose.dev.yml` ;
- erreurs détaillées et dépendances Composer de développement ;
- assets servis par Vite ;
- URL usuelle : `http://localhost:8080`.

### 3.2 Staging

- target Docker : `staging` ;
- Compose : `docker-compose.staging.yml` ;
- service public : `app`, port interne `80` ;
- domaine : `https://staging.barasira.com` ;
- base et volume dédiés au staging ;
- `APP_ENV=staging`, `APP_DEBUG=false`.

### 3.3 Production

- target Docker : `production` ;
- Compose : `docker-compose.prod.yml` ;
- service public : `nginx`, port interne `80` ;
- domaine : `https://barasira.com` ;
- `APP_ENV=production`, `APP_DEBUG=false`.

### 3.4 Construction de l’image

Le Dockerfile multi-stage :

1. installe les dépendances Node ;
2. compile les assets Vite ;
3. prépare PHP, Nginx, Supervisor et les extensions ;
4. installe les dépendances Composer ;
5. assemble la release ;
6. produit les targets développement, staging et production.

Le healthcheck appelle `http://127.0.0.1/up`. Cet endpoint valide Nginx,
PHP-FPM et le démarrage de Laravel.

## 4. Authentification et rôles

### 4.1 Mécanismes

- Session web sécurisée par cookie et jeton CSRF.
- Sanctum stateful pour l’interface Vue.
- Jeton personnel Bearer retourné par `POST /api/login` lorsqu’un
  `device_name` est fourni.
- Google SSO pour la connexion et la préparation d’une inscription.
- Vérification d’adresse électronique obligatoire pour les routes critiques.
- Réinitialisation de mot de passe par lien temporaire.

Le SSO Facebook est masqué dans l’interface et ne fait pas partie du contrat
actif.

### 4.2 Rôles

| Rôle | Capacités principales |
|---|---|
| `client` | créer une mission, comparer les candidatures, sélectionner, payer et évaluer |
| `prestataire` | gérer son profil, candidater, échanger et terminer une mission |
| `admin` | administrer utilisateurs, services, documents, partenaires et exports |
| `superadmin` | capacités admin, impersonation, journaux, base en lecture seule et traductions |

## 5. API

### 5.1 Conventions

- préfixe : `/api` ;
- représentation : JSON, sauf redirections OAuth/paiement ;
- authentification : cookie Sanctum ou `Authorization: Bearer <token>` ;
- validation : statut `422` et objet `errors` ;
- absence d’authentification : `401` ;
- accès interdit : `403` ;
- ressource absente : `404` ;
- limitation : `429`.

### 5.2 Familles d’endpoints

- Auth : Google SSO, inscription, connexion, déconnexion et utilisateur courant.
- Utilisateurs : consultation administrative et mise à jour du profil.
- Services et catégories : catalogue, recherche et administration.
- Missions : CRUD, génération assistée par IA, candidatures et sélection.
- Messagerie : conversations directes ou rattachées à une mission.
- Avis : publication et révision finale.
- CV et portfolio : ressources professionnelles.
- Paiements : initialisation, consultation, retours mobiles et webhook CinetPay.
- Traduction : traduction assistée par IA avec cache.

Les routes `/api/debug-auth`, `/api/documentation` et
`/api/oauth2-callback` sont des routes techniques, pas des endpoints métier.

## 6. Modèle de données

### 6.1 Agrégats principaux

- `users` : identité, rôle, coordonnées, locale, profil et vérification ;
- `service_categories`, `services` : catalogue ;
- `missions` : besoin client, lieu, dates, budget, durée, statut et assignation ;
- `applications` : candidature, type de prix, tarif et décision ;
- `payments` : montant, fournisseur, méthode, statut et allocation ;
- `messages` : conversation directe ou de mission ;
- `reviews`, `platform_reviews`, `client_comments` : réputation ;
- `documents` : justificatifs privés et décision administrative ;
- `partners`, `partner_promotions` : annuaire et mise en avant ;
- `mission_images`, `mission_invitations`, `mission_unassignments` :
  preuves et historique du cycle de mission.

### 6.2 CV et compétences

- `resumes`, `experiences`, `educations`, `certifications` ;
- `resume_languages`, `resume_tags`, `portfolio_items` ;
- `user_skills`.

### 6.3 Contraintes importantes

- slugs uniques pour services, missions et utilisateurs ;
- un avis de mission unique selon les participants définis ;
- documents stockés sur un disque privé ;
- contacts des partenaires non exposés publiquement ;
- candidatures incompatibles refusées lorsque les créneaux se chevauchent ;
- durée facturable non inférieure à la durée initiale ;
- allocations paiement : 90 % prestataire et 10 % plateforme.

## 7. Règles métier

### 7.1 Mission

1. Un client crée une mission `pending`.
2. Les prestataires proposent un tarif horaire ou global.
3. Le client accepte une candidature.
4. La mission passe à `in_progress` et les autres candidatures sont refusées.
5. Le prestataire assigné réalise puis termine la mission.
6. Le client initialise le paiement avec une à cinq preuves photographiques.
7. Les participants peuvent publier les évaluations autorisées.

### 7.2 Partenaires

- ordre d’affichage modifiable depuis l’administration ;
- publication contrôlée par `is_published` ;
- campagne sponsorisée active entre ses dates ;
- priorité des mises en avant par montant décroissant ;
- coordonnées de contact réservées à l’administration.

### 7.3 Localisation

Les langues applicatives sont le français (`fr`), l’anglais (`en`) et le
bambara (`bm`). La locale choisie est conservée dans un cookie et, pour un
utilisateur connecté, dans `users.locale`. Les validations, mails et
notifications utilisent la locale du destinataire.

## 8. Intégrations

### 8.1 Messagerie électronique

L’envoi transactionnel utilise Brevo :

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=contact@barasira.com
```

`MAIL_PASSWORD` est une clé SMTP Brevo, jamais une clé API/MCP. Les IP
sortantes de staging et production doivent être autorisées lorsque la sécurité
IP Brevo est activée. Cloudflare Email Routing reçoit les messages destinés à
`contact@barasira.com` et les redirige vers la boîte configurée.

### 8.2 Paiements

- CinetPay : mobile money et carte selon configuration ;
- PayPal : parcours web/mobile ;
- webhook CinetPay limité en fréquence ;
- les secrets et signatures sont configurés uniquement par variables
  d’environnement.

### 8.3 Notifications

- base de données Laravel ;
- e-mail ;
- SMS selon `SMS_DRIVER` ;
- WhatsApp selon `WHATSAPP_DRIVER` ;
- mode journalisé lorsque le fournisseur distant n’est pas configuré.

### 8.4 Intelligence artificielle

La configuration est centralisée dans `config/ai.php`. La génération de
mission préremplit un formulaire sans publier automatiquement. La traduction
est mise en cache et peut persister les variantes liées aux modèles.

## 9. Sécurité

- CSRF pour les parcours web ;
- cookies `Secure`, `HttpOnly` et `SameSite` selon configuration ;
- mots de passe hachés ;
- contrôles d’accès par middleware, rôles et Policies ;
- validation des fichiers, tailles, MIME et URL ;
- rate limiting sur IA, candidatures, paiements et formulaires publics ;
- masquage des secrets dans l’explorateur et les audits ;
- audit des mises à jour et suppressions Eloquent ;
- journaux administratifs accessibles uniquement au superadmin ;
- documents privés téléchargés après autorisation ;
- consentement préalable aux cookies marketing.

Les clés, mots de passe SMTP, secrets OAuth et identifiants de paiement ne
doivent jamais être committés ni intégrés aux images Docker.

## 10. Exploitation

### 10.1 Déploiement

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan superadmin:ensure
```

Ne jamais utiliser `migrate:fresh` ou `migrate:refresh` en staging ou
production : ces commandes peuvent détruire les données.

### 10.2 Journaux

- Laravel : `storage/logs/laravel-YYYY-MM-DD.log` ;
- PHP : `storage/logs/php-error.log` ;
- audit : `storage/logs/audit.log` ;
- Nginx : `/var/log/nginx/access.log` et `/var/log/nginx/error.log`.

### 10.3 Diagnostic

```bash
supervisorctl status
curl --fail http://127.0.0.1/up
php artisan migrate:status
php artisan about
```

Un `/up` local en `200` avec un timeout public indique généralement une
rupture entre le proxy Coolify et le port `80` du service applicatif.

### 10.4 Sauvegardes

- sauvegarde quotidienne MySQL chiffrée ;
- rétention séparée du serveur applicatif ;
- test de restauration périodique ;
- sauvegarde des documents privés et logos ;
- rotation des journaux ;
- procédure documentée de reprise.

## 11. Qualité et validation

```bash
php artisan test
./vendor/bin/pint --test
npm run build
npm run scss:audit
php artisan l5-swagger:generate
```

Une évolution d’API doit modifier simultanément :

1. routes et contrôleurs ;
2. Form Requests et autorisations ;
3. tests Feature ;
4. annotations `app/Swagger` ;
5. artefacts OpenAPI ;
6. README et présente spécification si l’architecture est affectée.

## 12. Documentation livrée

| Document | Usage |
|---|---|
| `README.md` | prise en main et référence développeur |
| `docs/SPECIFICATIONS-TECHNIQUES.md` | spécification technique maintenable |
| `docs/Specifications-Techniques-Barasira.pdf` | version partageable |
| `docs/business-model-barasira.html` | source du document stratégique |
| `docs/Business-Model-Barasira.pdf` | business model partageable |
| `storage/api-docs/api-docs.yaml` | contrat OpenAPI versionnable |
| `storage/api-docs/api-docs.json` | contrat utilisé par Swagger UI |

Les administrateurs et superadministrateurs accèdent à ce catalogue depuis
`/admin/documentation`. Les aperçus et téléchargements sont fournis par une
liste blanche côté serveur ; un identifiant inconnu retourne une réponse 404
et aucun chemin de fichier arbitraire n’est accepté.
