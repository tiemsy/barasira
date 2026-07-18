# Barasira

Barasira est une plateforme de mise en relation entre clients et prestataires de services, construite avec Laravel 12. Elle expose une API REST protégée par Sanctum et une interface Vue 3/Inertia.

## Sommaire

- Présentation
- Architecture
- Tables principales
- Tables CVThèque
- Démarrage rapide
- Rôles utilisateurs
- Commandes utiles
- Documentation API
- API REST
- Modules principaux
- Gestion des missions
- Messagerie client–prestataire
- Intelligence artificielle
- Frontend
- Qualité, tests et style
- Sécurité
- Déploiement
- Conventions d’architecture
- Journaux système superadministrateur

## Présentation

- Objectif : permettre aux clients de publier des missions et aux prestataires d’y postuler, de les exécuter et d’échanger avec les clients.
- Domaines couverts : utilisateurs, services, catégories, missions, candidatures, messagerie, paiements, avis et CV.

## Architecture

- Backend : Laravel 12 et PHP 8.2+
- Authentification : Laravel Sanctum, sessions web et vérification d’adresse e-mail
- API : contrôleurs dans `app/Http/Controllers/Api`
- Pages web : contrôleurs Inertia dans `app/Http/Controllers/Front`
- Validation : Form Requests dans `app/Http/Requests`
- Accès aux données : interfaces et implémentations Eloquent dans `app/Repositories`
- Autorisation : Policies et règles métier dédiées
- Frontend : Vue 3, Composition API et Inertia.js
- Accès API frontend : composables dans `resources/js/composables`
- Composants réutilisables : `resources/js/Components`
- Build : Vite et Sass
- Documentation API : annotations L5-Swagger et Swagger UI

## Journaux système superadministrateur

Le rôle `superadmin` dispose d’un visualiseur en lecture seule à l’adresse `/admin/logs`. Il affiche un nombre limité de lignes récentes provenant de Laravel, PHP, des accès Nginx et des erreurs Nginx. Les sources sont définies par une liste blanche dans `config/log_viewer.php` : aucun chemin arbitraire ne peut être demandé depuis l’interface.

Les chemins PHP et Nginx peuvent être adaptés avec `LOG_VIEWER_PHP_PATH`, `LOG_VIEWER_NGINX_ACCESS_PATH` et `LOG_VIEWER_NGINX_ERROR_PATH`. Laravel configure également PHP à l’exécution pour écrire ses erreurs natives dans `storage/logs/php-error.log`, ce qui évite de dépendre uniquement de la configuration de l’image Docker. Cette initialisation peut être désactivée avec `LOG_VIEWER_CONFIGURE_PHP_LOGGING=false`. Le processus PHP doit disposer d’un droit d’écriture sur le journal PHP et d’un droit de lecture sur les autres fichiers sélectionnés.

## Tables principales (12 tables métier)

1. users - Utilisateurs (clients, prestataires, admins)
2. services - Types de services proposés
3. missions - Missions créées par les clients
4. applications - Candidatures des prestataires
5. payments - Paiements / transactions
6. reviews - Avis et notes
7. messages - Messagerie interne
8. user_skills - Compétences / tarifs
9. notifications - Notifications utilisateurs
10. documents - Documents d'identité / justificatifs
11. disputes - Litiges entre utilisateurs
12. favorites - Prestataires favoris

## Tables CVThèque

- resumes - CV des utilisateurs
- experiences - Expériences professionnelles
- educations - Formations
- certifications - Certifications
- resume_languages - Langues parlées
- resume_tags - Tags de compétences
- portfolio_items - Éléments de portfolio

## Démarrage rapide en local

1. Démarrer les conteneurs Docker :

```bash
docker compose up -d
```

2. Préparer la base de données :

```bash
docker compose exec php php artisan db:create barasira
docker compose exec php php artisan migrate --seed
```

3. Installer et compiler les dépendances frontend :

```bash
npm install
npm run dev
```

Pour activer l’autocomplétion d’adresse Google Maps dans les formulaires de mission, activez **Maps JavaScript API** et **Places API** dans Google Cloud, puis ajoutez dans `.env` :

```dotenv
VITE_GOOGLE_MAPS_API_KEY=your_browser_api_key
```

Restreignez cette clé aux domaines autorisés et aux API Google Maps nécessaires. Sans clé, le champ d’adresse reste disponible en saisie manuelle.

Le nom du service PHP peut varier selon le fichier Docker Compose utilisé.

### Données de démonstration

Les seeders créent un scénario déterministe avec des clients, des prestataires spécialisés, cinq services, des missions à différents statuts, des compétences, des avis et des paiements cohérents. Ils peuvent être relancés sans dupliquer ces données.

Comptes principaux (mot de passe : `password`) :

- Client : `aminata.client@barasira.test`
- Client : `moussa.client@barasira.test`
- Prestataire : `ibrahim.electricien@barasira.test`
- Prestataire : `mariam.plombiere@barasira.test`
- Prestataire : `boubacar.informatique@barasira.test`

Le compte administrateur reste `admin@barasira.com` avec le mot de passe défini par `AdminSeeder`.

En local, un superadministrateur est créé avec `superadmin@barasira.com` et le mot de passe `superadmin123`. En staging et en production, configurez obligatoirement `SUPERADMIN_EMAIL` et un `SUPERADMIN_PASSWORD` d’au moins 12 caractères. `SUPERADMIN_PHONE`, `SUPERADMIN_FIRST_NAME` et `SUPERADMIN_LAST_NAME` sont personnalisables.

Après toute modification de ces variables sur un environnement utilisant le cache Laravel, exécutez `php artisan config:clear` avant le provisionnement, puis régénérez le cache avec `php artisan config:cache`.

Le provisionnement est idempotent et peut être exécuté après chaque déploiement :

```bash
php artisan superadmin:ensure
```

La commande crée le compte, restaure un compte précédemment supprimé ou met à jour ses informations et son mot de passe. `php artisan db:seed --force` l’exécute aussi automatiquement en staging et en production.

## Rôles utilisateurs

- client — Utilisateur standard (défaut)
- prestataire — Fournisseur de services
- admin — Administrateur

## Commandes utiles

Vérifier les routes :

```bash
php artisan route:list --path=api
```

Générer la documentation Swagger :

```bash
php artisan swagger:generate
```

Développement frontend :

```bash
npm run dev
npm run sass
npm run watch:sass
```

Vérification :

```bash
vendor/bin/pint
php artisan test
npm run build
```

Cache de production :

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Documentation API

- Interface Swagger UI : `/api/documentation`
- Sources OpenAPI : annotations L5-Swagger centralisées dans `app/Swagger`
- Spécifications générées pour Swagger UI : `storage/api-docs/api-docs.json` et `storage/api-docs/api-docs.yaml`
- La commande `php artisan swagger:generate` refuse la génération lorsqu’une route API manque dans la documentation ou lorsqu’une opération documentée n’existe plus.
- Chaque opération possède un `operationId` unique. Les contrôleurs sans route ni méthode, comme les placeholders OAuth/Refresh Token, ne créent pas d’endpoints fictifs.
- Routes principales (routes/api.php):
  - Auth: POST /api/login, POST /api/register, POST /api/logout, GET /api/me
  - Ressources: users, user-skills, service-categories, services, missions, messages, reviews, resumes, portfolio-items

## API REST

## Paiements en ligne

Le client propriétaire d’une mission peut régler le montant défini côté serveur via Orange Money, Moov Money ou carte bancaire avec le checkout CinetPay, ainsi que via PayPal. Aucun numéro de carte ni secret de portefeuille n’est stocké par Barasira. CinetPay confirme les paiements via `/api/payments/webhooks/cinetpay`, puis le serveur revérifie systématiquement la transaction auprès de l’API CinetPay avant de la marquer comme effectuée.

Configurez `CINETPAY_API_KEY`, `CINETPAY_SITE_ID` et `CINETPAY_SECRET_KEY`. Pour PayPal, configurez `PAYPAL_CLIENT_ID`, `PAYPAL_CLIENT_SECRET`, `PAYPAL_WEBHOOK_ID`, puis un taux marchand explicite `PAYPAL_XOF_PER_UNIT`, car PayPal ne traite pas directement le XOF. Utilisez les URLs sandbox avant la production et exécutez `php artisan migrate` après déploiement.

- Routes définies dans routes/api.php.
- Controllers dans app/Http/Controllers/Api/.
- Authentification: Sanctum (cookies ou tokens).
- Endpoints d’auth:
  - POST /api/login (middleware web + guest)
  - POST /api/register (middleware web)
  - POST /api/logout (auth:sanctum)
  - GET /api/me

## Modules principaux

- Users, Profiles (skills, resumes, portfolio)
- Services & Categories
- Missions
- Messagerie client–prestataire
- Reviews (avis/notations)
- Recherche de services (services-search)
- Génération et traduction assistées par IA

## Gestion des missions

Les routes de mission sont protégées par `auth:sanctum` et `verified`.

- `GET /api/missions` : missions de l’utilisateur connecté avec filtres et pagination
- `POST /api/missions` : création d’une mission par un client
- `GET /api/missions/{mission}` : détail d’une mission autorisée
- `PATCH /api/missions/{mission}` : modification ou transition de statut
- `DELETE /api/missions/{mission}` : suppression d’une mission en attente

Flux métier :

1. Le client crée une mission avec le statut `pending`.
2. Un prestataire postule.
3. Une candidature est acceptée et la mission passe à `in_progress`.
4. Le prestataire assigné termine la mission avec le statut `completed`.
5. Le client peut publier un avis.

Transitions actuellement contrôlées :

- Client : `pending` ou `in_progress` vers `cancelled`
- Prestataire assigné : `in_progress` vers `completed`
- Administrateur : gestion complète
- Les détails ne sont modifiables par le client que lorsque la mission est en attente

## Messagerie client–prestataire

La table `messages` prend en charge les conversations directes et les conversations associées à une mission.

Endpoints :

- `GET /api/messages` : liste des conversations et nombre total de messages non lus
- `GET /api/messages/conversation/{user}` : contenu d’une conversation
- `GET /api/messages/conversation/{user}?mission_id={id}` : conversation liée à une mission
- `POST /api/messages` : envoi d’un message

Règles :

- Les échanges sont limités aux couples client–prestataire.
- Un utilisateur ne peut pas s’envoyer de message.
- Une conversation de mission est autorisée pour le client et le prestataire assigné ou candidat.
- Les messages reçus sont marqués comme lus à l’ouverture de la conversation.
- La taille maximale d’un message est de 5 000 caractères.

Pages frontend :

- `/messages` : boîte de réception et fil de discussion
- `/messages/create?user={id}` : démarrage d’une conversation directe
- `/messages/create?user={id}&mission={id}` : démarrage d’une conversation de mission

## Intelligence artificielle

Routes protégées :

- POST `/api/missions/generate-with-ai`
- POST `/api/ai/translate`

- La génération IA ne publie jamais directement une mission : elle préremplit le formulaire.
- La traduction libre est mise en cache.
- `translateModelField()` persiste les traductions liées aux modèles.

La configuration des fournisseurs se trouve dans `config/ai.php` et utilise les variables d’environnement associées.

## Frontend

- Vue 3 avec Composition API et Inertia.js
- Layout partagé : `resources/js/Layouts/AppLayout.vue`
- Composants partagés : `resources/js/Components`
- Composables d’accès API : `resources/js/composables`
- Fonctions utilitaires : `resources/js/utils`
- Sass pour les styles globaux et styles `scoped` pour les composants isolés
- Vite pour le build et le HMR

## Qualité, tests et style

- Tests : PHPUnit
- Style PHP : Laravel Pint
- Build frontend : `npm run build`
- Debug : Laravel Debugbar en développement et Ignition pour les erreurs

Les tests locaux nécessitent une base de données accessible. La configuration Docker utilise généralement le nom d’hôte `barasira_db_1`, qui n’est résolu que depuis le réseau Docker.

## Sécurité

- Authentification Sanctum
- Vérification obligatoire de l’adresse e-mail pour les routes critiques
- Form Requests pour la validation
- Policies et services métier pour l’autorisation
- Soft deletes pour les utilisateurs
- Contrôle des participants pour les missions et conversations

## Déploiement

- Compiler les assets avec `npm run build`
- Exécuter les migrations avec `php artisan migrate --force`
- Provisionner le superadmin avec `php artisan superadmin:ensure`
- Générer les caches Laravel
- Configurer les variables d’environnement de la base, du mail, de Sanctum et des fournisseurs IA

## Conventions d’architecture

Avant toute modification :

1. Examiner les modèles, migrations, contrôleurs, repositories, routes et composants existants.
2. Réutiliser les composants, composables, helpers et services déjà disponibles.
3. Éviter toute duplication de validation, requête, autorisation, formatage ou interface.
4. Ajouter une abstraction uniquement lorsqu’elle est partagée ou représente une règle métier.

Principes :

- Les contrôleurs restent minces : validation, orchestration et réponse.
- La validation HTTP appartient aux Form Requests.
- Les règles d’accès appartiennent aux Policies ou services métier.
- Les requêtes réutilisables appartiennent aux repositories injectés par leur interface.
- Les réponses API répétées doivent utiliser des Resources.
- Les composants Vue ne doivent pas dupliquer la logique réseau : utiliser les composables.
- Les formats de nom, date, statut et erreur doivent être centralisés dans des helpers.
- Les listes doivent être paginées lorsque leur volume peut augmenter.
- Toute nouvelle fonctionnalité critique doit être couverte par des tests d’intégration.
