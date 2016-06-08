<?php

namespace Uparts\TireSync;

use Uparts\Http\HttpClient;
use Uparts\Model\VehicleFitment;

class TireSyncImpl implements TireSync
{
    const API_VERSION = 'v1';
    const DATABASE_IDENTIFIER = 'oe';

    private $serviceUrl;
    private $apiKey;
    private $requestorIpAddress;
    private $httpClient;

    public function __construct($serviceUrl, $apiKey, $requestorIpAddress, HttpClient $httpClient)
    {
        $this->serviceUrl = $serviceUrl;
        $this->apiKey = $apiKey;
        $this->requestorIpAddress = $requestorIpAddress;
        $this->httpClient = $httpClient;
    }

    public function fetchAvailableYears()
    {
        $url = $this->buildUrl(
            [ $this->serviceUrl
            , self::API_VERSION
            , self::DATABASE_IDENTIFIER
            , 'years'
            , $this->apiKey
            , base64_encode($this->requestorIpAddress)
            ]
        );

        /**
         * @todo: Use try/catch to handle HttpClient exceptions and rethrow as
         * TireSyncExceptions.
         */
        $result = $this->httpClient->get($url);

        return $result['items'];
    }

    public function fetchAvailableMakes($year)
    {
        $url = $this->buildUrl(
            [ $this->serviceUrl
            , self::API_VERSION
            , self::DATABASE_IDENTIFIER
            , 'makes'
            , $this->apiKey
            , base64_encode($this->requestorIpAddress)
            , base64_encode($year)
            ]
        );

        /**
         * @todo: Use try/catch to handle HttpClient exceptions and rethrow as
         * TireSyncExceptions.
         */
        $result = $this->httpClient->get($url);

        return $result['items'];
    }

    public function fetchAvailableModels($year, $make)
    {
        $url = $this->buildUrl(
            [ $this->serviceUrl
            , self::API_VERSION
            , self::DATABASE_IDENTIFIER
            , 'models'
            , $this->apiKey
            , base64_encode($this->requestorIpAddress)
            , base64_encode($year)
            , base64_encode($make)
            ]
        );

        /**
         * @todo: Use try/catch to handle HttpClient exceptions and rethrow as
         * TireSyncExceptions.
         */
        $result = $this->httpClient->get($url);

        return $result['items'];
        return [];
    }

    public function fetchAvailableOptions($year, $make, $model)
    {
        $url = $this->buildUrl(
            [ $this->serviceUrl
            , self::API_VERSION
            , self::DATABASE_IDENTIFIER
            , 'options'
            , $this->apiKey
            , base64_encode($this->requestorIpAddress)
            , base64_encode($year)
            , base64_encode($make)
            , base64_encode($model)
            ]
        );

        /**
         * @todo: Use try/catch to handle HttpClient exceptions and rethrow as
         * TireSyncExceptions.
         */
        $result = $this->httpClient->get($url);

        return $result['items'];
    }

    public function fetchVehicleFitments($year, $make, $model, $option)
    {
        $url = $this->buildUrl(
            [ $this->serviceUrl
            , self::API_VERSION
            , self::DATABASE_IDENTIFIER
            , 'vehicle_fitments'
            , $this->apiKey
            , base64_encode($this->requestorIpAddress)
            , base64_encode($year)
            , base64_encode($make)
            , base64_encode($model)
            , base64_encode($option)
            ]
        );

        /**
         * @todo: Use try/catch to handle HttpClient exceptions and rethrow as
         * TireSyncExceptions.
         */
        $result = $this->httpClient->get($url);
        $notes = $result['notes'];

        return array_map(
            function ($fitment) use ($notes) {
                return VehicleFitment::fromApiItem($fitment, $notes);
            },
            $result['items']
        );
    }

    public function fetchStandardFitment($year, $make, $model, $option)
    {
        $fitments = $this->fetchVehicleFitments($year, $make, $model, $option);

        $standardFitments = array_values(array_filter(
            $fitments,
            function ($fitment) {
                return $fitment->isStandard();
            }
        ));

        if ( count($standardFitments) > 0 )
        {
            return $standardFitments[0];
        }
        else
        {
            throw new TireSyncException("No standard fitment found for $year $make $model $option");
        }
    }

    private function buildUrl($pieces)
    {
        return implode('/', $pieces);
    }
}
