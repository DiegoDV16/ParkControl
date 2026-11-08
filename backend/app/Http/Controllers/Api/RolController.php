<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Http\JsonResponse;

class RolController extends Controller
{
    /**
     * Catálogo de roles para los selectores del frontend.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Rol::orderBy('idRol')->get(['idRol', 'rol']),
        ]);
    }
}
