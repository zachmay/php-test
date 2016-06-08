<?php

namespace Uparts\Handlers;

use Interop\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Uparts\TireSync\TireSyncException;

class FitmentsHandler
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get(Request $request, Response $response, $args)
    {
        $tireSync = $this->container->get('tireSync');

        $queryParameters = $request->getQueryParams();
        $year = $queryParameters['year'] ?? null;
        $make = $queryParameters['make'] ?? null;
        $model = $queryParameters['model'] ?? null;
        $option = $queryParameters['option'] ?? null;

        /**
         * @todo: We should probably check to see that the submitted year is in the array
         * of available years, the selected make in the array of available makes, etc.
         */
        $availableYears = $tireSync->fetchAvailableYears();
        $availableMakes = $year ? $tireSync->fetchAvailableMakes($year) : [];
        $availableModels = $make ? $tireSync->fetchAvailableModels($year, $make) : [];
        $availableOptions = $model ? $tireSync->fetchAvailableOptions($year, $make, $model) : [];

        $standardFitment = null;

        if ( $year && $make && $model && $option )
        {
            try
            {
                $standardFitment = $tireSync->fetchStandardFitment($year, $make, $model, $option);
                $error = null;
            }
            catch ( TireSyncException $e )
            {
                $error = $e->getMessage();
            }
        }

        $vars = 
            [ 'availableYears' => $availableYears
            , 'availableMakes' => $availableMakes
            , 'availableModels' => $availableModels
            , 'availableOptions' => $availableOptions
            , 'selectedYear' => $year
            , 'selectedMake' => $make
            , 'selectedModel' => $model
            , 'selectedOption' => $option
            , 'standardFitment' => $standardFitment
            , 'error' => $error
            ];

        $this->container
            ->view
            ->render($response, 'fitments.html', $vars);
    }
}
