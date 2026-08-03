<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *
 *   @OA\Info(
 *      title="Bara Sira API",
 *      version="1.3.0",
 *      description="Contrat de l’API BaraSira au 30 juillet 2026 : authentification web ou Bearer, utilisateurs, services, missions, candidatures avec tarification horaire ou globale, messagerie, avis, IA, CV, portfolio et paiements. Les routes protégées exigent aussi une adresse e-mail vérifiée.",
 *
 *      @OA\Contact(name="Équipe BaraSira", email="contact@BaraSira.com")
 *   ),
 *
 *   @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Serveur principal API"
 *   )
 * )
 */
class OpenApi {}
