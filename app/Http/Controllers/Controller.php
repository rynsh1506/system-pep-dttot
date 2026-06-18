<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: "DTTOT & PEP API", version: "1.0.0", description: "Dokumentasi API untuk pengecekan DTTOT dan PEP")]
#[OA\Server(url: "http://localhost:8000", description: "API Server")]
#[OA\SecurityScheme(securityScheme: "bearerAuth", type: "http", scheme: "bearer")]
abstract class Controller
{
    //
}
