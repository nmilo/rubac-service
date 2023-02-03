<?php

namespace Tests\Unit;

use App\Repositories\WorkflowRepository;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WorkflowRepositoryTest extends TestCase
{
    private $workflowRepository;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('paths', [
            '/admin/*',
            '/test/*'
        ]);

        Config::set('workflows', [
            [
                "WorkflowID" => 1,
                "WorkflowName" => "Test Workflow",
                "Path" => "/admin/*",
                "Params" => [],
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
            ],
            [
                "WorkflowID" => 2,
                "WorkflowName" => "Test Workflow",
                "Path" => "/test/*",
                "Params" => [],
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
            ]
        ]);

        $this->workflowRepository = new WorkflowRepository();
    }

    /** @test */
    public function it_gets_paths()
    {
        $res = $this->workflowRepository->getPaths();

        $this->assertEquals($res, [
            '/admin/*',
            '/test/*'
        ]);
    }

     /** @test */
     public function it_gets_workflows()
     {
         $res = $this->workflowRepository->getWorkflows();

         $this->assertEquals($res,  [
            [
                "WorkflowID" => 1,
                "WorkflowName" => "Test Workflow",
                "Path" => "/admin/*",
                "Params" => [],
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
            ],
            [
                "WorkflowID" => 2,
                "WorkflowName" => "Test Workflow",
                "Path" => "/test/*",
                "Params" => [],
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
            ]
        ]);
    }

    /** @test */
    public function it_gets_workflow_for_path()
    {
        $res = $this->workflowRepository->getWorkflowsForPath('/admin/settings');

         $this->assertEquals($res,  [
            [
                "WorkflowID" => 1,
                "WorkflowName" => "Test Workflow",
                "Path" => "/admin/*",
                "Params" => [],
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
            ]
        ]);
    }

    /** @test */
    public function it_matches_url()
    {
        $res = $this->workflowRepository->matchUrl('/admin/settings', '/admin/*');
        $this->assertTrue($res);
    }
}
