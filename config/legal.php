<?php

return [
    'updated_at' => '22 juillet 2026',
    'contact_email' => env('LEGAL_CONTACT_EMAIL', env('MAIL_CONTACT_ADDRESS', 'contact@barasira.com')),
    'documents' => [
        'cgu' => [
            'title' => 'Conditions Générales d’Utilisation', 'short' => 'CGU',
            'intro' => 'Les présentes conditions encadrent l’accès et l’utilisation de Barasira, plateforme de mise en relation entre clients et prestataires de services.',
            'sections' => [
                ['title' => '1. Objet et acceptation', 'paragraphs' => ['L’utilisation de Barasira implique l’acceptation des présentes CGU. Un utilisateur qui refuse ces conditions ne doit pas créer de compte ni utiliser les fonctionnalités réservées.', 'Barasira peut faire évoluer les CGU. La version applicable est celle publiée sur la plateforme à la date d’utilisation.']],
                ['title' => '2. Comptes et exactitude des informations', 'paragraphs' => ['L’utilisateur fournit des informations exactes, actuelles et personnelles, protège ses identifiants et signale rapidement tout accès non autorisé.', 'Les comptes créés sous une fausse identité, utilisés frauduleusement ou compromettant la sécurité de la plateforme peuvent être suspendus.']],
                ['title' => '3. Mise en relation et responsabilités', 'paragraphs' => ['Barasira facilite la rencontre entre clients et prestataires. Sauf indication expresse, Barasira n’exécute pas elle-même les missions proposées et ne devient pas partie au contrat de prestation conclu entre utilisateurs.', 'Chaque utilisateur vérifie l’adéquation du service, les qualifications, le prix, les délais et les conditions d’intervention avant de s’engager.']],
                ['title' => '4. Comportements interdits', 'paragraphs' => ['Sont interdits : les contenus illicites ou trompeurs, le harcèlement, la fraude, l’usurpation d’identité, le contournement des mesures de sécurité, la collecte non autorisée de données et toute utilisation portant atteinte aux droits d’autrui.']],
                ['title' => '5. Suspension, disponibilité et responsabilité', 'paragraphs' => ['Barasira peut modérer, limiter ou suspendre un accès en cas de risque, de fraude ou de violation des règles. La plateforme s’efforce d’assurer la continuité du service sans garantir une disponibilité permanente.', 'La responsabilité de Barasira ne peut être engagée pour les actes d’un utilisateur ou d’un prestataire indépendant, sous réserve des règles impératives applicables.']],
                ['title' => '6. Droit applicable et contact', 'paragraphs' => ['Les présentes CGU sont régies par le droit malien. Les parties recherchent d’abord une solution amiable avant toute procédure devant la juridiction compétente.', 'Toute question peut être adressée à :contact_email.']],
            ],
        ],
        'cgv' => [
            'title' => 'Conditions Générales de Vente', 'short' => 'CGV',
            'intro' => 'Ces conditions s’appliquent aux services payants proposés ou facilités par Barasira, notamment les commissions de plateforme et publications sponsorisées.',
            'sections' => [
                ['title' => '1. Champ d’application', 'paragraphs' => ['Les caractéristiques essentielles, le prix et la durée d’un service payant sont présentés avant validation. Les conditions particulières affichées lors de la commande complètent les présentes CGV.']],
                ['title' => '2. Prix et commande', 'paragraphs' => ['Les prix sont indiqués en francs CFA (FCFA), toutes taxes applicables comprises lorsqu’elles sont dues. La commande devient définitive après confirmation du paiement ou validation expresse par Barasira.', 'Pour une publication sponsorisée, le contenu, la période de diffusion et les éléments créatifs doivent être validés avant mise en ligne.']],
                ['title' => '3. Paiement', 'paragraphs' => ['Les paiements sont traités par les prestataires affichés au moment du règlement. Barasira ne conserve pas les données complètes de carte bancaire ni les codes secrets de portefeuille mobile.', 'Une transaction peut rester en attente jusqu’à confirmation sécurisée du prestataire de paiement. Toute tentative de fraude peut entraîner un refus ou une vérification complémentaire.']],
                ['title' => '4. Exécution, annulation et remboursement', 'paragraphs' => ['Les conditions d’annulation d’une mission dépendent de son état d’avancement et des engagements convenus entre client et prestataire. Une demande de remboursement est examinée au regard des preuves disponibles et des règles du moyen de paiement utilisé.', 'Une campagne sponsorisée déjà diffusée n’est remboursable que pour la partie non exécutée lorsque l’interruption est imputable à Barasira, sauf accord différent.']],
                ['title' => '5. Réclamations et litiges', 'paragraphs' => ['Toute réclamation doit préciser la référence de transaction, la mission ou campagne concernée et les justificatifs utiles. Elle peut être envoyée à :contact_email.', 'Les parties privilégient une résolution amiable. À défaut, le droit malien et les juridictions compétentes s’appliquent, sans priver le consommateur de ses droits impératifs.']],
            ],
        ],
        'confidentialite' => [
            'title' => 'Politique de confidentialité', 'short' => 'Confidentialité',
            'intro' => 'Cette politique explique comment Barasira collecte et utilise les données personnelles conformément à la loi malienne n°2013-015 du 21 mai 2013 modifiée.',
            'sections' => [
                ['title' => '1. Données collectées', 'paragraphs' => ['Selon votre usage : identité, coordonnées, profil professionnel, services, missions, messages, avis, données de connexion, informations de transaction et documents nécessaires à une vérification.', 'Barasira ne conserve pas les numéros complets de carte ni les codes secrets de paiement mobile.']],
                ['title' => '2. Finalités', 'paragraphs' => ['Les données servent à créer et sécuriser les comptes, mettre en relation les utilisateurs, exécuter les paiements, prévenir la fraude, traiter les demandes, modérer les contenus, respecter les obligations légales et améliorer le service.', 'La mesure publicitaire et le marketing reposent sur votre consentement lorsqu’ils utilisent des cookies optionnels.']],
                ['title' => '3. Destinataires et transferts', 'paragraphs' => ['Les données sont accessibles aux équipes habilitées et, dans la mesure nécessaire, aux prestataires techniques, de paiement, d’hébergement, de communication ou de vérification. Elles peuvent être communiquées aux autorités lorsqu’une obligation légale l’impose.', 'Tout transfert est encadré par des mesures adaptées à la sensibilité des données et aux règles applicables.']],
                ['title' => '4. Durées et sécurité', 'paragraphs' => ['Les données sont conservées pendant la durée utile au compte, au contrat, au règlement des litiges et aux obligations légales. Les justificatifs KYC et données de transaction peuvent suivre les délais imposés aux prestataires financiers.', 'Barasira applique des mesures de contrôle d’accès, de chiffrement des échanges, de journalisation et de sauvegarde proportionnées aux risques.']],
                ['title' => '5. Vos droits', 'paragraphs' => ['Vous pouvez demander l’accès, la rectification, la mise à jour ou, lorsque la loi le permet, la suppression et l’opposition au traitement de vos données en écrivant à :contact_email.', 'Vous pouvez également saisir l’Autorité de Protection des Données à caractère Personnel du Mali (APDP).']],
            ],
        ],
        'cookies' => [
            'title' => 'Politique de cookies', 'short' => 'Cookies',
            'intro' => 'Cette politique décrit les traceurs utilisés par Barasira et la manière dont vous contrôlez les cookies optionnels.',
            'sections' => [
                ['title' => '1. Cookies nécessaires', 'paragraphs' => ['Ils assurent la session, l’authentification, la protection CSRF, la sécurité, la mémorisation du consentement et les fonctions demandées. Ils ne peuvent pas être désactivés depuis le gestionnaire de préférences sans empêcher certaines fonctions.']],
                ['title' => '2. Cookies marketing et mesure', 'paragraphs' => ['Avec votre accord, Google Analytics peut mesurer l’audience et Meta Pixel peut mesurer l’efficacité des campagnes. Ces outils ne sont chargés qu’après acceptation explicite.', 'La liste effective dépend des identifiants activés dans la configuration de Barasira. Aucun refus n’empêche l’accès aux fonctions essentielles.']],
                ['title' => '3. Durée du choix', 'paragraphs' => ['Votre choix de consentement est conservé pendant six mois. Les durées propres aux cookies tiers peuvent varier selon leur fournisseur et leur configuration.']],
                ['title' => '4. Modifier ou retirer le consentement', 'paragraphs' => ['Le lien « Gérer les cookies » présent dans le pied de page permet d’accepter ou de refuser les cookies marketing à tout moment. Le retrait bloque les nouveaux dépôts et déclenche la suppression des principaux cookies marketing accessibles à Barasira.']],
            ],
        ],
        'moderation' => [
            'title' => 'Charte de modération', 'short' => 'Modération',
            'intro' => 'Cette charte protège des échanges professionnels, utiles et respectueux sur Barasira.',
            'sections' => [
                ['title' => '1. Contenus concernés', 'paragraphs' => ['La charte couvre les profils, services, missions, messages, avis, images, documents et publications sponsorisées diffusés sur Barasira.']],
                ['title' => '2. Règles de publication', 'paragraphs' => ['Les contributions doivent être sincères, pertinentes, compréhensibles et respecter la vie privée, la propriété intellectuelle et la dignité des personnes.', 'Sont interdits les menaces, propos haineux ou discriminatoires, contenus sexuels illicites, escroqueries, faux avis, spam, coordonnées divulguées sans autorisation et accusations non étayées.']],
                ['title' => '3. Mesures de modération', 'paragraphs' => ['Selon la gravité et la répétition : demande de modification, limitation de visibilité, retrait, avertissement, suspension ou fermeture du compte. Les contenus manifestement illicites ou les risques sérieux peuvent être signalés aux autorités compétentes.']],
                ['title' => '4. Signalement et recours', 'paragraphs' => ['Un signalement doit identifier le contenu, expliquer le motif et fournir les éléments utiles. L’auteur concerné peut demander un réexamen à :contact_email.', 'La modération tient compte du contexte, de la sécurité des personnes, de la liberté d’expression et des obligations légales.']],
            ],
        ],
        'kyc' => [
            'title' => 'Politique KYC relative aux paiements', 'short' => 'KYC et paiements',
            'intro' => 'Cette politique décrit les vérifications d’identité susceptibles d’être requises pour sécuriser les paiements et respecter le cadre de lutte contre le blanchiment et le financement du terrorisme dans l’UMOA.',
            'sections' => [
                ['title' => '1. Rôle de Barasira et des prestataires de paiement', 'paragraphs' => ['Barasira facilite le paiement de missions au moyen de prestataires tiers. Les établissements de paiement et de monnaie électronique restent responsables de leurs contrôles réglementaires KYC selon leurs propres procédures.', 'Barasira peut collecter ou transmettre les informations strictement nécessaires lorsqu’elle agit comme intermédiaire autorisé ou pour prévenir une fraude.']],
                ['title' => '2. Vérifications possibles', 'paragraphs' => ['Selon le risque, le montant ou la demande du prestataire : nom, date et lieu de naissance, adresse, téléphone, pièce d’identité valide, photographie, activité, bénéficiaire effectif pour une organisation, origine ou destination des fonds.', 'Des contrôles renforcés peuvent concerner les personnes politiquement exposées, les transactions inhabituelles, les incohérences d’identité ou les opérations dépassant un seuil réglementaire.']],
                ['title' => '3. Refus ou suspension', 'paragraphs' => ['Un paiement, retrait ou compte peut être temporairement limité lorsque les informations sont incomplètes, expirées, incohérentes ou lorsqu’une vérification réglementaire est requise. Barasira ne garantit pas l’acceptation d’une transaction par le prestataire de paiement.']],
                ['title' => '4. Confidentialité et conservation', 'paragraphs' => ['Les documents KYC sont protégés par des accès restreints et ne sont utilisés que pour la vérification, la sécurité, les litiges et les obligations légales. Leur conservation respecte les durées applicables au prestataire assujetti et la législation sur les données personnelles.']],
                ['title' => '5. Références et contact', 'paragraphs' => ['Cette politique tient compte de la loi uniforme LBC/FT/FP publiée par la BCEAO et de l’Instruction BCEAO n°003-03-2025 relative à l’identification, la vérification de l’identité et la connaissance de la clientèle par les institutions financières.', 'Pour une question ou une contestation relative à une vérification : :contact_email.']],
            ],
        ],
    ],
];
