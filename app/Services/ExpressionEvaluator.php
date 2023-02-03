<?php

namespace App\Services;

use App\Traits\ExpressionHelpers;

/**
 * Class responsible for evaluating expressions
 */
class ExpressionEvaluator
{
    use ExpressionHelpers;

    /**
     * Locally scoped parameters
     */
    public $variables = [];

    /**
     * Populate variables array with given params
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $param) {
            $this->variables[$param['name']] = $param['value'];
        }
    }

    /**
     * Prepare expression for execution
     *
     * @pparam string $expression
     *
     * @return string
     */
    public function parseExpression(string $expression): string
    {
        $expression = $this->replaceDotNotation($expression);
        $expression = $this->replaceVariables($expression);
        $expression = $this->prefixMethodsWithThis($expression);

        return $expression;
    }

    public function evaluate(string $expression)
    {
        $expression = $this->parseExpression($expression);

        return eval("return ($expression);");
    }

    /**
     * @param string $expression
     *
     * @return string
     */
    public function replaceVariables(string $expression): string
    {
        // Find variables in expression
        preg_match('/\$[a-zA-z0-9-_]*\b/', $expression, $variablesInExpression);
        foreach ($variablesInExpression as $variable)
        {
            $variableName = substr($variable, 1);
            $expression = str_replace($variable, "\$this->variables['$variableName']", $expression);
        }

        return $expression;
    }

    public function replaceDotNotation(string $expression): string
    {
        $matched = preg_match('/\$[a-zA-z0-9-_]*\.\b/i', $expression);

        if($matched > 0) {
            return str_replace('.', '->', $expression).'()';
        }

        return $expression;
    }

    public function prefixMethodsWithThis(string $expression): string
    {
        $matched = preg_match('/^[a-zA-z0-9-_]*\(/i', $expression);

        if($matched > 0) {
            return '$this->'.$expression;
        }

        return $expression;
    }
}