<?php

namespace App\Services;

use App\Http\Requests\BaseRequest;
use App\Models\User;
use App\Repositories\WorkflowRepository;
use App\Traits\ExpressionHelpers;

class RuleValidatorService
{
    use ExpressionHelpers;

    /**
     * @var WorkflowRepository $workflowRepository
     */
    protected $workflowRepository;

    /**
     * @var User $user
     */
    protected User $user;

    /**
     * @var BaseRequest $request
     */
    protected BaseRequest $request;

    /**
     * @var $path
     */
    protected string $requestUrl;

    /**
     * Params populated from workflow
     *
     * @var array $params
     */
    protected $params = [];

    /**
     * @param WorkflowRepository $workflowRepository
     */
    public function __construct(WorkflowRepository $workflowRepository)
    {
        $this->workflowRepository = $workflowRepository;
    }

    /**
     * @param User $user
     * @param BaseRequest $request
     */
    public function setBaseParams(User $user, BaseRequest $request)
    {
        $this->user = $user;
        $this->request = $request;
        $this->requestUrl = substr($request->getPath(), 3);
    }

    /**
     * Set params array, as defined in workflow rule
     *
     * @param array $params
     */
    public function setParams(array $params){
        foreach ($params as $param) {
            $this->params[$param['Name']] = $this->evalExpression($this->parseParamExpression($param['Expression']));
        }
    }

    /**
     * Core function of RUBAC service. Validates if user is allowed to access resource based on workflow rules
     *
     * @param User $user
     * @param BaseRequest $request
     *
     * @return bool
     */
    public function validate(User $user, BaseRequest $request): bool
    {
        $this->setBaseParams($user, $request);
        $workflows = $this->workflowRepository->getWorkflowsForPath($this->requestUrl);

        foreach ($workflows as $workflow) {
            $validated = $this->validateWorkflow($workflow);

            if(!$validated) return false;
        }

        return true;
    }

    public function evalExpression($expression)
    {
        return eval("return $expression");
    }

    public function replaceParamVariables($expression)
    {
        preg_match('/\$[a-zA-z0-9-_]*\b/', $expression, $variables);
        foreach($variables as $variable)
        {
            $variableName = substr($variable, 1);
            $expression = str_replace($variable, "\$this->params['$variableName']", $expression);
        }

        return $expression;
    }

    public function parseParamExpression($expression)
    {
        $expression = str_replace('$', '$this->', $expression);
        $expression = str_replace('.', '->', $expression).'();';

        return $expression;
    }

    /**
     * @param array $workflow
     *
     * @return bool
     */
    public function validateWorkflow($workflow): bool
    {
        $this->setParams($workflow['Params']);

        foreach ($workflow['Rules'] as $rule) {
            $validated = $this->validateRule($rule);

            if(!$validated) return false;
        }

        return true;
    }

    /**
     * @param array $rule
     *
     * @return bool
     */
    public function validateRule(array $rule): bool
    {
        $expression = $this->replaceParamVariables($rule['Expression']);

        return eval("return ($expression);");
    }
}