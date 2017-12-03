<?php

namespace Deviny\Excelify\Middleware;

use Closure;
use App;

class Localization
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
        App::setLocale(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        return $next($request);
    }
}
