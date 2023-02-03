<?php

namespace App\Repositories;

use App\Interfaces\WorkflowRepositoryInterface;
use Illuminate\Support\Arr;

/**
 * Class responsible for fetching workflows.
 * Currently workflows are stored in config file, however that can be replaced with another storage driver in future
 */
class WorkflowRepository implements WorkflowRepositoryInterface {

    /**
     * Get all workflows
     */
    public function getWorkflows()
    {
        return config('workflows');
    }

    /**
     * Get workflows for given path
     *
     * @param string $path
     */
    public function getWorkflowsForPath(string $path)
    {
        $workflows = $this->getWorkflows();

        $filteredWorkflows = Arr::where($workflows, function ($value, $key) use ($path) {
            return $this->matchUrl($path, $value['Path']);
        });

        return $filteredWorkflows;
    }

    /**
     * Get array of routes that have defined rules
     */
    public function getPaths()
    {
        return config('paths');
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