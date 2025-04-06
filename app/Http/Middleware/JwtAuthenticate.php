<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token expirado. Por favor, faça login novamente.'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token inválido. Por favor, faça login novamente.'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token não fornecido. Você precisa estar logado para acessar esta rota.'], 401);
        }

        return $next($request);
    }
} 