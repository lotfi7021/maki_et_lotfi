<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "API Auth JWT",
    version: "1.0.0",
    description: "API d authentification securisee avec JWT"
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Serveur local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class SwaggerInfo {}