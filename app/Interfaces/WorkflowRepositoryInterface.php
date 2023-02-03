<?php

namespace App\Interfaces;

interface WorkflowRepositoryInterface {

    public function getWorkflows();

    public function getWorkflowsForPath(string $path);

    public function getPaths();

}