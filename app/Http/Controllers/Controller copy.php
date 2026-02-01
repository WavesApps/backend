<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Rashid Backend API Documentation",
 *      description="Laravel API Documentation for Rashid Backend Application",
 *      @OA\Contact(
 *          email="admin@rashid.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Rashid Backend API Server"
 * )
 *
 * @OA\PathItem(
 *     path="/api"
 * )
 */
abstract class Controller
{
    //
}
