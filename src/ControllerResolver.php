<?php

namespace Test;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolver implements ControllerResolverInterface
{
    protected $testControllers;

    public function __construct($testControllers)
    {
        $this->testControllers = $testControllers;
    }

    public function getController(Request $request)
    {
        $testName = (string) $request->query->get('test');

        if(array_key_exists($testName, $this->testControllers)) {
            return $this->testControllers[$testName];
        }

        return null;
    }

    public function getArguments(Request $request, $controller)
    {
        return [];
    }
}