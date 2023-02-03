<?php

namespace App\Http\Middleware;

use App\Http\Requests\BaseRequest;
use App\Services\RouteMatchService;
use App\Services\RuleValidatorService;
use Closure;
use Illuminate\Http\Response;

class RUBACMiddleware
{
    /**
     * @var RouteMatchService $routeMatchService
     */
    protected $routeMatchService;

    /**
     * @var RuleValidatorService $ruleValidatorService
     */
    protected $ruleValidatorService;

    /**
     * @param RouteMatchService $routeMatchService
     * @param RuleValidatorService $ruleValidatorService
     */
    public function __construct(RouteMatchService $routeMatchService, RuleValidatorService $ruleValidatorService)
    {
        $this->routeMatchService = $routeMatchService;
        $this->ruleValidatorService = $ruleValidatorService;
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
        return $this->ruleValidatorService->validate($request->user(), $request);
    }
}
