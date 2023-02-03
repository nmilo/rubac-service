<?php

namespace Tests\Unit;

use App\Services\ExpressionEvaluator;
use PHPUnit\Framework\TestCase;

class ExpressionEvaluatorTest extends TestCase
{
    private $expressionEvaluator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->expressionEvaluator = new ExpressionEvaluator();
    }


    /** @test */
    public function it_handles_dot_notation()
    {
        $expression = '$request.getIpAddress';
        $expectedExpression = '$request->getIpAddress()';

        $this->assertEquals($this->expressionEvaluator->replaceDotNotation($expression), $expectedExpression);
    }

    /** @test */
    public function it_does_not_replace_dots()
    {
        $expression = '100.100.100.100';
        $expectedExpression = '100.100.100.100';

        $this->assertEquals($this->expressionEvaluator->replaceDotNotation($expression), $expectedExpression);
    }

    /** @test */
    public function it_replaces_variables()
    {
        $this->expressionEvaluator->setParams([
            [
                'name' => 'test',
                'value' => 123
            ]
        ]);

        $expression = '$test == 123';
        $expectedExpression = '$this->variables[\'test\'] == 123';

        $this->assertEquals($this->expressionEvaluator->replaceVariables($expression), $expectedExpression);
    }

    /** @test */
    public function it_handles_scenario_1()
    {
        $this->expressionEvaluator->setParams([
            [
                'name' => 'request',
                'value' => 123
            ]
        ]);

        $expression = '$request.getIpAddress';
        $expectedExpression = '$this->variables[\'request\']->getIpAddress()';

        $expression = $this->expressionEvaluator->parseExpression($expression);

        $this->assertEquals($expression, $expectedExpression);
    }

    /** @test */
    public function it_handles_scenario_2()
    {
        $this->expressionEvaluator->setParams([
            [
                'name' => 'user',
                'value' => 123
            ]
        ]);

        $expression = '$user.getRole';
        $expectedExpression = '$this->variables[\'user\']->getRole()';

        $expression = $this->expressionEvaluator->parseExpression($expression);

        $this->assertEquals($expression, $expectedExpression);
    }

    /** @test */
    public function it_handles_scenario_3()
    {
        $this->expressionEvaluator->setParams([
            [
                'name' => 'ip_address',
                'value' => '100.100.100.100'
            ]
        ]);

        $expression = '$ip_address == \'100.100.100.100\'';
        $expectedExpression = '$this->variables[\'ip_address\'] == \'100.100.100.100\'';

        $expression = $this->expressionEvaluator->parseExpression($expression);

        $this->assertEquals($expression, $expectedExpression);
    }

    /** @test */
    public function it_handles_scenario_4()
    {
        $this->expressionEvaluator->setParams([
            [
                'name' => 'user_role',
                'value' => 'ADMIN'
            ]
        ]);

        $expression = '$user_role == \'ADMIN\'';
        $expectedExpression = '$this->variables[\'user_role\'] == \'ADMIN\'';

        $expression = $this->expressionEvaluator->parseExpression($expression);

        $this->assertEquals($expression, $expectedExpression);
    }

    /** @test */
    public function it_handles_scenario_5()
    {
        $expression = 'ip_range($ip_address, \'100.100.100.1/28\')';
        $expectedExpression = '$this->ip_range($this->variables[\'ip_address\'], \'100.100.100.1/28\')';

        $expression = $this->expressionEvaluator->parseExpression($expression);

        $this->assertEquals($expression, $expectedExpression);
    }

    /** @test */
    public function it_handles_scenario_6()
    {
        $expression = 'in($user_role, \'ADMIN\', \'SUPER_ADMIN\')';
        $expectedExpression = '$this->in($this->variables[\'user_role\'], \'ADMIN\', \'SUPER_ADMIN\')';

        $expression = $this->expressionEvaluator->parseExpression($expression);

        $this->assertEquals($expression, $expectedExpression);
    }
}
