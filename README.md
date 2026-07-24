# Barasira

Barasira est une plateforme de mise en relation entre clients et prestataires de services, construite avec Laravel 12. Elle expose une API REST protégée par Sanctum et une interface Vue 3/Inertia.

## Sommaire

- Présentation
- Architecture
- Référencement SEO
- Tables principales
- Tables CVThèque
- Partenaires
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
- Exploration de la base et traductions superadministrateur
- Business model et prévisions

## Présentation

- Objectif : permettre aux clients de publier des missions et aux prestataires d’y postuler, de les exécuter et d’échanger avec les clients.
- Domaines couverts : utilisateurs, services, catégories, missions, candidatures, invitations, messagerie, paiements, avis, CV professionnels et partenaires.
- Les utilisateurs vérifiés peuvent publier et modifier un avis public sur Barasira depuis `/avis`, indépendamment des évaluations de prestataires liées aux missions.
- L’accueil présente les trois avis publiés les plus récents, leur moyenne globale et un lien vers la page complète.
- Les pages publiques de détail des services et missions utilisent des slugs lisibles plutôt que des identifiants numériques.

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

## Référencement SEO

Le référencement est centralisé dans `app/Support/SeoMeta.php` et `config/seo.php`. Les métadonnées sont rendues dans le HTML initial par Laravel, puis actualisées lors des navigations Inertia par `resources/js/Components/SeoHead.vue`.

Fonctionnalités disponibles :

- titres et descriptions propres à l’accueil, au catalogue, aux fiches services, aux partenaires et au contact ;
- URL canoniques sans paramètres de recherche ;
- balises Open Graph et Twitter pour le partage sur les réseaux sociaux ;
- données structurées Schema.org `Organization`, `WebSite`, `Service` et `BreadcrumbList` ;
- URL publiques de services basées sur les slugs ;
- directive `index,follow` limitée aux pages publiques utiles ;
- directive `noindex,nofollow` sur l’authentification, les profils, tableaux de bord et espaces administratifs ;
- sitemap dynamique contenant les pages publiques et uniquement les services actifs.

Points d’accès :

- Sitemap XML : `/sitemap.xml` (pages publiques et services actifs)
- Directives des robots : `/robots.txt`
- Image sociale par défaut : `/images/logo-barasira.png`

Configuration de production :

```dotenv
APP_URL=https://barasira.com
SEO_SITE_NAME=Barasira
SEO_DEFAULT_TITLE="Barasira — Trouvez un prestataire fiable au Mali"
SEO_DEFAULT_DESCRIPTION="Trouvez rapidement des prestataires qualifiés au Mali pour vos travaux, services à domicile et besoins professionnels."
SEO_DEFAULT_IMAGE=/images/logo-barasira.png
SEO_COUNTRY=Mali
```

Après une modification de cette configuration :

```bash
php artisan optimize:clear
php artisan config:cache
```

Checklist après déploiement :

1. Vérifier que `APP_URL` utilise le domaine HTTPS définitif.
2. Ouvrir `/robots.txt` et `/sitemap.xml` sur le domaine public.
3. Ajouter et valider le domaine dans Google Search Console.
4. Soumettre `https://barasira.com/sitemap.xml` dans Search Console.
5. Tester l’accueil et plusieurs fiches services avec l’outil Google de résultats enrichis.
6. Surveiller l’indexation, les erreurs d’exploration, les requêtes et le taux de clics.
7. Publier régulièrement des contenus utiles ciblant les métiers et villes du Mali, puis développer des avis et liens entrants de qualité.

Tests dédiés :

```bash
php artisan test --filter=SeoTest
```

Le SEO technique facilite l’exploration et l’indexation, mais ne garantit pas une position précise dans Google. Le classement dépend également de la concurrence, de la qualité et de la fraîcheur des contenus, de l’expérience utilisateur, de la notoriété du domaine et des liens entrants.

## Journaux système superadministrateur

Le rôle `superadmin` dispose d’un visualiseur à l’adresse `/admin/logs`. Il affiche les dernières lignes de l’audit métier, de Laravel, de PHP et de Nginx. Il peut purger toutes les sources accessibles ou uniquement les entrées comprises entre deux dates, après confirmation. La purge est elle-même retracée dans l’audit. Les sources sont définies par une liste blanche dans `config/log_viewer.php` : aucun chemin arbitraire ne peut être demandé depuis l’interface.

Les modifications et suppressions Eloquent sont écrites dans `storage/logs/audit.log` avec l’acteur, la route, l’adresse IP et les valeurs avant/après ; les champs sensibles sont masqués. Le chemin est configurable avec `AUDIT_LOG_PATH` et le fichier est créé au démarrage. Les chemins PHP et Nginx peuvent être adaptés avec `LOG_VIEWER_PHP_PATH`, `LOG_VIEWER_NGINX_ACCESS_PATH` et `LOG_VIEWER_NGINX_ERROR_PATH`. Le processus PHP doit disposer des droits de lecture et d’écriture nécessaires aux sources qui doivent être purgées.

## Exploration de la base et traductions superadministrateur

Deux outils supplémentaires sont réservés au rôle `superadmin` :

- `/admin/database` liste toutes les tables de la connexion principale, leurs colonnes, types, caractère obligatoire ou nullable, puis leurs données paginées par 25, 50 ou 100 lignes. La structure reste visible pour une table vide. Cet espace n’expose aucune opération d’écriture ; les mots de passe, jetons, clés et secrets sont masqués.
- `/admin/translations` traduit un texte de 10 000 caractères maximum depuis le français, l’anglais ou le bambara vers toutes les autres langues prises en charge. Les résultats restent modifiables et copiables avant utilisation. Le moteur dépend de `AI_TRANSLATION_PROVIDER`, puis de `AI_PROVIDER` par défaut.

Les routes sont protégées par le middleware `role:superadmin` et leurs contrôleurs vérifient également le rôle. L’explorateur n’accepte que les noms de tables obtenus depuis le schéma actif, ce qui empêche de fournir un identifiant SQL arbitraire.

## Tables métier principales

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
13. mission_images - Photos de réalisations associées aux missions
14. mission_invitations - Invitations directes envoyées aux prestataires
15. mission_unassignments - Historique et motifs de désaffectation
16. partners - Entreprises partenaires, contacts et visibilité publique
17. partner_promotions - Campagnes payantes et planifiées de mise en avant
18. platform_reviews - Avis des utilisateurs sur la plateforme Barasira

## Tables CVThèque

- resumes - CV des utilisateurs
- experiences - Expériences professionnelles
- educations - Formations
- certifications - Certifications
- resume_languages - Langues parlées
- resume_tags - Tags de compétences
- portfolio_items - Éléments de portfolio

Les prestataires peuvent gérer leurs diplômes, expériences et certifications depuis leur profil. Ces informations sont visibles par les clients lorsqu’elles appartiennent à un CV public.

## Partenaires

Les rôles `admin` et `superadmin` gèrent les partenaires depuis `/admin/partners` :

- informations de l’entreprise et présentation publique ;
- personne à contacter et coordonnées réservées à l’administration ;
- logo, site web et ordre d’affichage ;
- statut brouillon ou publié ;
- campagne de mise en avant avec montant payé et période de validité.

Seuls les champs publics des partenaires publiés sont envoyés aux visiteurs. Une sélection apparaît sur la page d’accueil et la liste complète est disponible sur `/partners`. Lorsqu’un site web est renseigné, le logo ouvre ce site dans un nouvel onglet sécurisé.

La zone supérieure de l’accueil peut présenter jusqu’à deux partenaires sponsorisés. Une campagne est active uniquement entre ses dates de début et de fin ; les partenaires éligibles sont classés par montant payé décroissant. Le montant reste privé et n’est jamais envoyé aux visiteurs.

La page `/partners/sponsoring` permet à une entreprise de sélectionner puis d’envoyer par e-mail une demande de publication sponsorisée : 7 jours à 50 000 FCFA, 30 jours à 150 000 FCFA ou 90 jours à 350 000 FCFA. Le destinataire se configure avec `PARTNER_REQUEST_EMAIL` (ou, à défaut, `MAIL_CONTACT_ADDRESS`).

## Vérification des prestataires et administration

- Les prestataires déposent leurs pièces privées depuis leur profil (PDF/JPEG/PNG/WebP, 10 Mo maximum).
- L’administration filtre les documents par prestataire, catégorie de service, statut et type.
- Un document peut être validé, refusé avec motif, remis en attente ou supprimé définitivement.
- Le badge « Profil vérifié » dépend d’au moins une pièce d’identité validée ; il est recalculé après chaque décision ou suppression.
- Les erreurs d’upload trop volumineux, notamment HTTP 413, sont présentées clairement à l’utilisateur.
- Les admins et superadmins exportent en CSV compatible Excel les utilisateurs, services, missions et partenaires en conservant les filtres actifs.

## Cookies et mesure marketing

Une bannière distingue les cookies nécessaires, toujours actifs, des cookies marketing optionnels. Le visiteur peut accepter, refuser ou modifier son choix depuis le pied de page. Aucun outil marketing ne doit être initialisé avant le consentement correspondant. Voir `/legal/cookies` et `resources/js/composables/useCookieConsent.js`.

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

Les seeders créent un scénario déterministe avec des clients, des prestataires spécialisés, quatorze services, des missions à différents statuts, des compétences, des diplômes, des expériences, des certifications, des avis sur les prestataires, six avis sur la plateforme Barasira, des paiements et des fiches nécessaires au test du module partenaires. Ils peuvent être relancés sans dupliquer ces données.

Les partenaires présentés par Barasira sont **Urgol Events Mali** et **Les Petits Stylos**. Les coordonnées en `.test` et les logos générés par les seeders servent uniquement aux environnements de démonstration ; les données et droits de publication doivent être validés avant la production.

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
- superadmin — Superadministrateur avec accès aux journaux, à l’explorateur de base en lecture seule et à l’atelier de traduction

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
  - Paiements: POST /api/missions/{mission}/payments, GET /api/payments/{payment}, retours mobiles et webhook CinetPay

Les endpoints d’affichage utilisent les slugs :

- `GET /api/services/{service:slug}`
- `GET /api/missions/{mission:slug}`

Les endpoints de modification et de suppression conservent actuellement les identifiants internes afin de ne pas modifier les contrats d’administration existants.

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
- Profils prestataires (diplômes, expériences, certifications)
- Services & Categories
- Missions, invitations, affectation et désaffectation motivée
- Messagerie client–prestataire
- Reviews (avis/notations)
- Partenaires et visibilité publique
- Recherche de services (services-search)
- Génération et traduction assistées par IA

## Gestion des missions

Les routes de mission sont protégées par `auth:sanctum` et `verified`.

- `GET /api/missions` : missions de l’utilisateur connecté avec filtres et pagination
- `POST /api/missions` : création d’une mission par un client
- `GET /api/missions/{mission:slug}` : détail d’une mission autorisée via son slug
- `PATCH /api/missions/{mission}` : modification ou transition de statut
- `DELETE /api/missions/{mission}` : suppression d’une mission en attente
- `POST /api/missions/{mission}/claim` : affectation d’une mission disponible à un prestataire autorisé

Flux métier :

1. Le client crée une mission avec le statut `pending`.
2. Un prestataire postule, réclame une mission compatible ou reçoit une invitation directe du client.
3. Une candidature ou invitation est acceptée et la mission passe à `in_progress`.
4. Le prestataire assigné termine la mission avec le statut `completed`.
5. Le client peut publier un avis.

Le client propriétaire peut désaffecter le prestataire depuis la modale dédiée. Il doit sélectionner un motif prédéfini ; le motif « autre » impose une explication. Chaque désaffectation est historisée dans `mission_unassignments`.

Les invitations peuvent être notifiées par e-mail et SMS. La configuration SMS se trouve dans `config/sms.php` et utilise notamment `SMS_DRIVER`, `SMS_SENDER` et les variables du fournisseur retenu.

Les notifications métier utilisent également les canaux e-mail et WhatsApp. Le canal WhatsApp se configure avec `WHATSAPP_DRIVER`, `WHATSAPP_HTTP_ENDPOINT`, `WHATSAPP_HTTP_TOKEN` et `WHATSAPP_SENDER`. En l’absence de fournisseur HTTP configuré, le canal est journalisé sans bloquer la notification e-mail.

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
- Icônes SVG internes via `DashboardIcon`, sans dépendance aux classes Font Awesome dans les pages applicatives

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
- Contacts des partenaires exclus des réponses publiques
- Validation stricte des logos et des URLs de partenaires
- Historisation des désaffectations de prestataires
- Documents prestataires conservés sur le disque privé et téléchargés après contrôle d’accès
- Audit global des modifications et suppressions avec masquage des secrets
- Consentement préalable aux cookies marketing
- Exploration SQL limitée au schéma connu, sans route d’écriture et avec masquage des colonnes sensibles

## Déploiement

- Compiler les assets avec `npm run build`
- Exécuter les migrations avec `php artisan migrate --force`
- Créer le lien public des fichiers avec `php artisan storage:link`
- Provisionner le superadmin avec `php artisan superadmin:ensure`
- Générer les caches Laravel
- Configurer les variables d’environnement de la base, du mail, de Sanctum et des fournisseurs IA
- Configurer les passerelles de paiement, SMS et les URLs publiques avant la mise en production
- Configurer `APP_URL` et les variables `SEO_*`, puis contrôler `/robots.txt` et `/sitemap.xml`
- Enregistrer le domaine dans Google Search Console et soumettre le sitemap après la mise en ligne

## Business model et prévisions

Les documents stratégiques sont disponibles dans `docs/` :

- [`Business-Model-Barasira.pdf`](docs/Business-Model-Barasira.pdf) — présentation prête à partager ;
- [`business-model-barasira.html`](docs/business-model-barasira.html) — source HTML imprimable.

La version actuelle présente le modèle de commission à 10 %, la mise en avant sponsorisée, la stratégie marketing et partenaires avec Urgol Events Mali et Les Petits Stylos, l’organigramme de démarrage, le besoin de financement recommandé de 85 millions FCFA pour douze mois et un prévisionnel simplifié sur cinq ans. Ces hypothèses de pilotage doivent être validées avec un expert-comptable et les partenaires concernés avant usage contractuel ou levée de fonds.

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
