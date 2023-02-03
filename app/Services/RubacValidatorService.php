<?php

namespace App\Services;

use App\Http\Requests\BaseRequest;
use App\Models\User;
use App\Repositories\WorkflowRepository;

/**
 * Service responsible for validating rubac rules
 */
class RubacValidatorService
{
    /**
     * @var WorkflowRepository $workflowRepository
     */
    protected $workflowRepository;

    /**
     * @var ExpressionEvaluator $expressionEvaluator
     */
    protected ExpressionEvaluator $expressionEvaluator;

    /**
     * @param WorkflowRepository $workflowRepository
     */
    public function __construct(WorkflowRepository $workflowRepository, ExpressionEvaluator $expressionEvaluator)
    {
        $this->workflowRepository = $workflowRepository;
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * @param User $user
     * @param BaseRequest $request
     */
    public function setBaseParams(User $user, BaseRequest $request)
    {
        $params = [
            [
                'name' => 'user',
                'value' => $user
            ],
            [
                'name' => 'request',
                'value' => $request
            ]
        ];

        $this->expressionEvaluator->setParams($params);
    }

    /**
     * Set params array, as defined in workflow rule
     *
     * @param array $params
     */
    public function setWorkflowParams(array $workflowParams)
    {
        $params = [];
        foreach ($workflowParams as $param) {

            $params[] = [
                'name' => $param['Name'],
                'value' => $this->expressionEvaluator->evaluate($param['Expression'])
            ];
        }

        $this->expressionEvaluator->setParams($params);
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

        $workflows = $this->workflowRepository->getWorkflowsForPath(substr($request->getPath(), 3));
        foreach ($workflows as $workflow) {
            $validated = $this->validateWorkflow($workflow);

            if(!$validated) return false;
        }

        return true;
    }

    /**
     * @param array $workflow
     *
     * @return bool
     */
    public function validateWorkflow($workflow): bool
    {
        $this->setWorkflowParams($workflow['Params']);

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
        return $this->expressionEvaluator->evaluate($rule['Expression']);
    }
}