<?php

namespace App\Http\Middleware;

use App\Services\ExternalApi\Bitrix\BitrixUserInterface as BitrixUser;
use Closure;

class CheckAbilityMiddleware
{

    public function __construct(
        protected BitrixUser $user
    ) {}

    public function handle($request, Closure $next, $ability)
    {
        // TODO: Gates
//        Gate::allows($ability);
        $func = match($ability) {
            'kpi_shops' => function (BitrixUser $user) {
                return $user->isAdmin();
            },
            'kpi_shop' => function (BitrixUser $user, $request) {
                return $user->isAdmin() || $user->isManagerOf($request->shop_id ?? '');
            },
            'kpi_worker' => function (BitrixUser $user, $request) {
                return $user->isAdmin() ||
                    $user->isManagerOf($request->shop_id ?? '') ||
                    $user->isWorkerOf($request->shop_id ?? '');
            },
            'schedule_edit_worker' => function (BitrixUser $user, $request) {
                return $user->isAdmin() ||
                    $user->isManagerOf($request->get('shop_id'));
            },
            default => function () { return true; }
        };
        if (!$func($this->user, $request)) {
            throw new \Exception('Not Auth');
        }

        $response = $next($request);

        return $response;
    }
}
