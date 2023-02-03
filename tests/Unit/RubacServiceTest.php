<?php

namespace Tests\Unit;

use App\Repositories\WorkflowRepository;
use App\Services\RubacValidatorService;
use PHPUnit\Framework\TestCase;

class RubacServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RubacValidatorService(new WorkflowRepository());
    }

    /** @test */
    public function it_validates_false_rule()
    {
        $rule = [
            "RuleName" => "Rule",
            "Expression" => "1 == 2"
        ];

        $this->assertFalse($this->service->validateRule($rule));
    }

    /** @test */
    public function it_validates_correct_rule()
    {
        $rule = [
            "RuleName" => "Rule",
            "Expression" => "1 == 1"
        ];

        $this->assertTrue($this->service->validateRule($rule));
    }

    /** @test */
    public function it_parses_param_expression()
    {
        $expression = '$user.getRole';
        $expectedExpression = '$this->user->getRole();';

        $this->assertEquals($this->service->parseParamExpression($expression), $expectedExpression);
    }

    /** @test */
    public function it_evaluates_expression()
    {
        $expression = '1+2;';
        $expectedResult = '3';

        $this->assertEquals($this->service->evalExpression($expression), $expectedResult);
    }

    /** @test */
    public function it_replaces_param_variables()
    {
        $expression = '$user_role == "ADMIN"';
        $expectedExpression = '$this->params[\'user_role\'] == "ADMIN"';

        $this->assertEquals($this->service->replaceParamVariables($expression), $expectedExpression);
    }

    /** @test */
    public function it_validates_false_workflow()
    {

        $workflow = [
            "WorkflowID" => 1,
            "WorkflowName" => "Test Workflow",
            "Path" => "/test",
            "Params" => [
                [
                    "Name" => "test_var",
                    "Expression" => 'array'
                ]
            ],
            "Rules" => [
                [
                    "RuleName" => "Rule that passes",
                    "Expression" => "1 == 1"
                ],
                [
                    "RuleName" => "Rule that fails",
                    "Expression" => "1 == 2"
                ],
            ]
        ];


        $this->assertFalse($this->service->validateWorkflow($workflow));
    }

    /** @test */
    public function it_validates_true_workflow()
    {

        $workflow = [
            "WorkflowID" => 1,
            "WorkflowName" => "Test Workflow",
            "Path" => "/test",
            "Params" => [
                [
                    "Name" => "test_var",
                    "Expression" => 'array'
                ]
            ],
            "Rules" => [
                [
                    "RuleName" => "Rule that passes",
                    "Expression" => "1 == 1"
                ],
                [
                    "RuleName" => "Rule that passes",
                    "Expression" => "2 == 2"
                ],
            ]
        ];


        $this->assertTrue($this->service->validateWorkflow($workflow));
    }
}
