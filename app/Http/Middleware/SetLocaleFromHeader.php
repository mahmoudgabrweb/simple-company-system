<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleFromHeader
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('lang', 'ar'); // default to Arabic
        App::setLocale(in_array($lang, ['en', 'ar']) ? $lang : 'ar');

        return $next($request);
    }
}
