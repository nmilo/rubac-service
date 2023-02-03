<?php

namespace App\Http\Middleware;

use App\Http\Requests\BaseRequest;
use App\Repositories\WorkflowRepository;
use App\Services\RouteMatchService;
use App\Services\RubacValidatorService;
use Closure;
use Illuminate\Http\Response;

class RUBACMiddleware
{
    /**
     * @var RubacValidatorService $rubacValidatorService
     */
    protected $rubacValidatorService;

    /**
     * @var WorkflowRepository $workflowRepository
     */
    protected $workflowRepository;

    /**
     * @param RouteMatchService $rubacValidatorService
     * @param WorkflowRepository $workflowRepository
     */
    public function __construct(RubacValidatorService $rubacValidatorService, WorkflowRepository $workflowRepository)
    {
        $this->rubacValidatorService = $rubacValidatorService;
        $this->workflowRepository = $workflowRepository;
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
        // Remove api prefix
        $path = substr($request->getPath(), 3);

        $workflowsForPath = $this->workflowRepository->getWorkflowsForPath($path);

        if ($workflowsForPath && !$this->accessGranted($request, $workflowsForPath)) {
            return response()->json([
                'access_granted' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    /**
     * @param BaseRequest $request
     *
     * @return bool
     */
    protected function accessGranted(BaseRequest $request, $workflows): bool
    {
        return $this->rubacValidatorService->validate($request->user(), $request, $workflows);
    }
}
