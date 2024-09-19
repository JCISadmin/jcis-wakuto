<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MAdminUser;

class CheckViewPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->viewPermissionFlg === MAdminUser::VIEW_PERMISSION_FLG_OFF) {
            // 権限がない場合は403エラーを返す
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
