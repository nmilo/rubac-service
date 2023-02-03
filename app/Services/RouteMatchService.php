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
    public function ruleExistsForPath($url): bool
    {
        // trim api/ prefix
        $url = substr($url, 3);

        $paths = $this->workflowRepository->getPaths();
        foreach ($paths as $path) {
            $match = $this->matchUrl($url, $path);
            if ($match) return true;
        }

        return false;
    }

    /**
     * Match url with rule path
     * @param $url Url to check
     * @param $rulePath Url of given rule
     *
     * @return bool
     */
    public function matchUrl($url, $rulePath): bool
    {
        $pattern = preg_quote($rulePath, '/');
        $pattern = str_replace('\*', '.*', $pattern);
        $matched = preg_match('/^' . $pattern . '$/i', $url);
        if ($matched > 0) {
            return true;
        }

        return false;
    }
}