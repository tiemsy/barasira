<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *      title="Bara Sira API",
 *      version="1.0.0",
 *      description="Documentation complète de l'API de Bara Sira avec Swagger"
 *   ),
 *   @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Serveur principal API"
 *   )
 * )
 */
class OpenApi {}
