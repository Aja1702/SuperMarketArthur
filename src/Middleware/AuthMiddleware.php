<?php

namespace App\Middleware;

/**
 * Middleware de Autenticación
 * 
 * Verifica que el usuario esté autenticado antes de acceder a rutas protegidas
 */
class AuthMiddleware
{
    /**
     * Verificar sesión activa
     */
    public function handle($request, $next)
    {
        // Verificar si hay sesión iniciada
        if (!isset($_SESSION['user_id'])) {
            return redirect('/auth/login');
        }

        // Pasar al siguiente middleware/controlador
        return $next($request);
    }

    /**
     * Verificar role de usuario
     */
    public function requireRole($role)
    {
        return function($request, $next) use ($role) {
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
                return response(['error' => 'Acceso denegado'], 403);
            }
            return $next($request);
        };
    }

    /**
     * Refresco de sesión
     */
    public function refreshSession()
    {
        if (isset($_SESSION['user_id'])) {
            $_SESSION['last_activity'] = time();
        }
    }
}
