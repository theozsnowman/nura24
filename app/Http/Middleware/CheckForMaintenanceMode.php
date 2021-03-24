<?php

namespace App\Http\Middleware;

use Closure;

use DB;

use App\Models\Core;

use App\Models\User;

use View;

use Auth;

class CheckForMaintenanceMode
{
 
    public function handle($request, Closure $next)
    {

        // check if site is offline
        if(Core::config()->site_offline=='yes') {
            // exclude admin area
            if ($request->is('admin*') or $request->is('login*') or $request->is('logout*')) return $next($request);
            else return response()->make(view('frontend/'.Core::config()->template.'/offline'), 404);
        }           

        return $next($request);
    }

}
