<?php

namespace App\Http\Middleware;

use App\Http\Requests\BaseRequest;
use App\Services\RouteMatchService;
use App\Services\RubacValidatorService;
use Closure;
use Illuminate\Http\Response;

class RUBACMiddleware
{
    /**
     * @var RouteMatchService $routeMatchService
     */
    protected $routeMatchService;

    /**
     * @var RubacValidatorService $rubacValidatorService
     */
    protected $rubacValidatorService;

    /**
     * @param RouteMatchService $routeMatchService
     * @param RubacValidatorService $rubacValidatorService
     */
    public function __construct(RouteMatchService $routeMatchService, RubacValidatorService $rubacValidatorService)
    {
        $this->routeMatchService = $routeMatchService;
        $this->rubacValidatorService = $rubacValidatorService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(BaseRequest $request, Closure $next)
    {
        if ($this->routeMatchService->ruleExistsForPath($request->getPath())){

            if (!$this->passesRules($request)) {
                return response()->json([
                    'access_granted' => false,
                ], Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }

    /**
     * @param BaseRequest $request
     *
     * @return bool
     */
    protected function passesRules(BaseRequest $request): bool
    {
        return $this->rubacValidatorService->validate($request->user(), $request);
    }
}
