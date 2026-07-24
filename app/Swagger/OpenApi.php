<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *
 *   @OA\Info(
 *      title="Bara Sira API",
 *      version="1.2.0",
 *      description="API Barasira : authentification, utilisateurs, services, missions, candidatures avec tarification horaire ou globale, messagerie, avis, IA, CV, portfolio et paiements."
 *   ),
 *
 *   @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Serveur principal API"
 *   )
 * )
 */
class OpenApi {}
