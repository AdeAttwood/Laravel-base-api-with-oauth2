<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class VerifyResourceRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $req = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $server = \App::make('oauth2');
        $bridgedRequest  = \OAuth2\HttpFoundationBridge\Request::createFromRequest($req);
        if (!$server->verifyResourceRequest($bridgedRequest)) {
            return $server->getResponse()->send();
            die;
        } else {
            return $next($request);
        }
    }
}
