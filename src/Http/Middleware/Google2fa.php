<?php

namespace Lara\Jarvis\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA as TwoFactor;
use Illuminate\Support\Facades\Auth;

class Google2fa
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle (Request $request, Closure $next)
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }
        $user = Auth::user();
        if ($user->two_factor_enable) {
            if (isset($request->secret)) {
                if ((new TwoFactor ())->verifyKey($user->secret_key, $request->secret)) {
                    return $next($request);
                } else {
                    return response()->json(['message' => 'Código Inválido'], 422);
                }
            } else {
                return response()->json(['message' => 'Código de Autenticação de Dois Fatores é obrigatório (secret)'], 422);
            }
        } else {
            return response()->json(['message' => 'Autenticação não está habilitada.'], 422);
        }
    }
}
