<?php

namespace App\Repositories;

use Illuminate\Support\Arr;

/**
 * Class responsible for fetching workflows.
 * Currently workflows are stored in config file, however that can be replaced with another storage driver in future
 */
class WorkflowRepository {

    public function getWorkflows()
    {
        return config('workflows');
    }

    public function getWorkflowsForPath(string $path)
    {
        $workflows = $this->getWorkflows();

        $filteredWorkflows = Arr::where($workflows, function ($value, $key) use ($path) {
            return $this->matchUrl($path, $value['Path']);
        });

        return $filteredWorkflows;
    }

    /**
     *
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