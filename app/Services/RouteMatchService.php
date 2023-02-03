<?php

namespace App\Services;

use App\Repositories\WorkflowRepository;

class RouteMatchService
{
    /**
     * @var WorkflowRepository
     */
    public $workflowRepository;

    /**
     * @param WorkflowRepository $workflowRepository
     */
    public function __construct(
        WorkflowRepository $workflowRepository
    )
    {
        $this->workflowRepository = $workflowRepository;
    }

    /**
     * Check if workflow rule exists for given path
     * @param $url
     *
     * @return bool
     */
    public function ruleExistsForRoute($url): bool
    {
        $paths = $this->workflowRepository->getPaths();

        foreach ($paths as $path) {
            $match = $this->workflowRepository->matchUrl($url, $path);
            if ($match) return true;
        }

        return false;
    }
}