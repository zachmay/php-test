<?php

namespace Tests\TireSync;

use PHPUnit_Framework_TestCase;
use Mockery;

use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

use Uparts\Handlers\FitmentsHandler;

class FitmentsHandlerTest extends PHPUnit_Framework_TestCase
{
    public function test_fitments_handler_works()
    {
        $queryParams = [];
        $availableYears = ["2006"];

        $viewVariables =
            [ 'availableYears' => $availableYears
            , 'availableMakes' => []
            , 'availableModels' => []
            , 'availableOptions' => []
            , 'selectedYear' => null
            , 'selectedMake' => null
            , 'selectedModel' => null
            , 'selectedOption' => null
            , 'standardFitment' => null
            , 'error' => null
            ];

        $tireSync = Mockery::mock(TireSync::class, function($mock) use ($availableYears) {
            $mock->shouldReceive('fetchAvailableYears')
                 ->andReturn($availableYears);
        });

        $request = Mockery::mock(Request::class, function($mock) use ($queryParams) {
            $mock->shouldReceive('getQueryParams')
                 ->andReturn($queryParams);
        });

        $response = Mockery::mock(Response::class, function($mock) {
            $mock->shouldReceive('getStatusCode')
                 ->andReturn(200);

            $mock->shouldReceive('getReasonPhrase')
                 ->andReturn('OK');

            $mock->shouldReceive('getProtocolVersion')
                 ->andReturn('1.1');

            $mock->shouldReceive('getHeaders')
                 ->andReturn([]);

            $mock->shouldReceive('getBody')
                 ->andReturn('');
        });

        $view = Mockery::mock(Twig::class, function($mock) use ($response, $viewVariables) {
            $mock->shouldReceive('render')
                 ->with($response, 'fitments.html', $viewVariables);
        });

        $container = Mockery::mock(Container::class, function($mock) use ($tireSync, $view) {
            $mock->shouldReceive('get')
                 ->with('tireSync')
                 ->andReturn($tireSync);

            $mock->view = $view;
        });

        $handler = new FitmentsHandler($container);

        $handler->get($request, $response, []);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function setUp()
    {
    }
}
