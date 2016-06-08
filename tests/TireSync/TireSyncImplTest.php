<?php

namespace Tests\TireSync;

use PHPUnit_Framework_TestCase;
use Mockery;
use Uparts\Http\HttpClient;
use Uparts\Model\VehicleFitment;
use Uparts\TireSync\TireSyncException;

use Uparts\TireSync\TireSyncImpl;

class TireSyncImplTest extends PHPUnit_Framework_TestCase
{
    public function test_available_years_works()
    {
        $requestUrl = $this->serviceUrl
                    . '/v1/oe/years/'
                    . $this->apiKey
                    . '/'
                    . base64_encode($this->requestorIp);

        $years = [ "2006", "2007", "2008" ];

        $response =
            [ "data_version" => 1605
            , "error_message" =>  ""
            , "request_type" => "years"
            , "items" => $years
            , "parameters" =>
                [ "year" => 2006
                ]
            ];
        $this->scenario($requestUrl, $response);

        $tireSync = new TireSyncImpl($this->serviceUrl, $this->apiKey, $this->requestorIp, $this->httpClient);
        $result = $tireSync->fetchAvailableYears();

        $this->assertEquals($years, $result);
    }

    public function test_fetch_makes_works()
    {
        $year = "2012";
        $requestUrl = $this->serviceUrl
                    . '/v1/oe/makes/'
                    . $this->apiKey
                    . '/'
                    . base64_encode($this->requestorIp)
                    . '/'
                    . base64_encode($year);

        $makes =
            [ "Audi"
            , "Ford"
            , "Subaru" 
            ];

        $response =
            [ "data_version" => 1605
            , "error_message" =>  ""
            , "request_type" => "makes"
            , "items" => $makes
            , "parameters" =>
                [ "year" => (int) $year
                ]
            ];
        $this->scenario($requestUrl, $response);

        $tireSync = new TireSyncImpl($this->serviceUrl, $this->apiKey, $this->requestorIp, $this->httpClient);
        $result = $tireSync->fetchAvailableMakes($year);

        $this->assertEquals($makes, $result);

    }

    public function test_fetch_models_works()
    {
        $year = "2012";
        $make = "Ford";

        $requestUrl = $this->serviceUrl
                    . '/v1/oe/models/'
                    . $this->apiKey
                    . '/'
                    . base64_encode($this->requestorIp)
                    . '/'
                    . base64_encode($year)
                    . '/'
                    . base64_encode($make);

        $models =
            [ "Crown Victoria"
            , "E-150"
            , "E-250" 
            ];

        $response =
            [ "data_version" => 1605
            , "error_message" =>  ""
            , "request_type" => "models"
            , "items" => $models
            , "parameters" =>
                [ "year" => (int) $year
                , "make" => $make
                ]
            ];
        $this->scenario($requestUrl, $response);

        $tireSync = new TireSyncImpl($this->serviceUrl, $this->apiKey, $this->requestorIp, $this->httpClient);
        $result = $tireSync->fetchAvailableModels($year, $make);

        $this->assertEquals($models, $result);
    }

    public function test_fetch_available_options_works()
    {
        $year = "2012";
        $make = "Ford";
        $model = "Crown Victoria";

        $requestUrl = $this->serviceUrl
                    . '/v1/oe/options/'
                    . $this->apiKey
                    . '/'
                    . base64_encode($this->requestorIp)
                    . '/'
                    . base64_encode($year)
                    . '/'
                    . base64_encode($make)
                    . '/'
                    . base64_encode($model);

        $options =
            [ "Base"
            , "LX"
            , "LX Sport"
            , "Police Interceptor"
            ];
            
        $response =
            [ "data_version" => 1605
            , "error_message" =>  ""
            , "request_type" => "options"
            , "items" => $options
            , "parameters" =>
                [ "year" => (int) $year
                , "make" => $make
                , "model" => $model
                ]
            ];
        $this->scenario($requestUrl, $response);

        $tireSync = new TireSyncImpl($this->serviceUrl, $this->apiKey, $this->requestorIp, $this->httpClient);
        $result = $tireSync->fetchAvailableOptions($year, $make, $model);

        $this->assertEquals($options, $result);
    }

    public function test_fetch_vehicle_fitments_works()
    {
        $year = "2012";
        $make = "Ford";
        $model = "Crown Victoria";
        $option = "Police Interceptor";

        $notes = "Blah blah blah";

        $requestUrl = $this->serviceUrl
                    . '/v1/oe/vehicle_fitments/'
                    . $this->apiKey
                    . '/'
                    . base64_encode($this->requestorIp)
                    . '/'
                    . base64_encode($year)
                    . '/'
                    . base64_encode($make)
                    . '/'
                    . base64_encode($model)
                    . '/'
                    . base64_encode($option);

        $fitments =
            [ [ "CARTIREID" => 254243605615
              , "STDOROPT" => "S"
              , "FRB" => "B"
              , "LOADINDEX" => "98"
              , "SPEEDRATING" => "W"
              , "SIZEDESCRIPTION" => "P235/55R17 98W"
              , "PREFIX" => "P"
              , "LOADDESCRIPTION" => null
              , "SECTIONWIDTH" => "235"
              , "ASPECTRATIO" => "55"
              , "RIM" => "17"
              , "FITMENT" => "Standard OE       "
              ]
            ];
            
        $response =
            [ "data_version" => 1605
            , "error_message" =>  ""
            , "request_type" => "options"
            , "items" => $fitments
            , "parameters" =>
                [ "year" => (int) $year
                , "make" => $make
                , "model" => $model
                , "option" => $option
                ]
            , "notes" => $notes
            ];
        $this->scenario($requestUrl, $response);

        $tireSync = new TireSyncImpl($this->serviceUrl, $this->apiKey, $this->requestorIp, $this->httpClient);
        $result = $tireSync->fetchVehicleFitments($year, $make, $model, $option);

        $fitmentObjects = array_map(
            function ($fitment) use ($notes) {
                return VehicleFitment::fromApiItem($fitment, $notes);
            },
            $fitments
        );

        $this->assertEquals($fitmentObjects, $result);
    }

    public function test_fetch_standard_fitment_works()
    {
        $year = "2012";
        $make = "Ford";
        $model = "Crown Victoria";
        $option = "Police Interceptor";

        $notes = "Blah blah blah";

        $requestUrl = $this->serviceUrl
                    . '/v1/oe/vehicle_fitments/'
                    . $this->apiKey
                    . '/'
                    . base64_encode($this->requestorIp)
                    . '/'
                    . base64_encode($year)
                    . '/'
                    . base64_encode($make)
                    . '/'
                    . base64_encode($model)
                    . '/'
                    . base64_encode($option);

        $optional =
            [ "CARTIREID" => 999999
            , "STDOROPT" => "O"
            , "FRB" => "B"
            , "LOADINDEX" => "98"
            , "SPEEDRATING" => "W"
            , "SIZEDESCRIPTION" => "P235/55R17 98W"
            , "PREFIX" => "P"
            , "LOADDESCRIPTION" => null
            , "SECTIONWIDTH" => "235"
            , "ASPECTRATIO" => "55"
            , "RIM" => "17"
            , "FITMENT" => "The Optional One"
            ];

        $standard =
            [ "CARTIREID" => 254243605615
            , "STDOROPT" => "S"
            , "FRB" => "B"
            , "LOADINDEX" => "98"
            , "SPEEDRATING" => "W"
            , "SIZEDESCRIPTION" => "P235/55R17 98W"
            , "PREFIX" => "P"
            , "LOADDESCRIPTION" => null
            , "SECTIONWIDTH" => "235"
            , "ASPECTRATIO" => "55"
            , "RIM" => "17"
            , "FITMENT" => "Standard OE       "
            ];
            
        $fitments = [ $optional, $standard ];

        $response =
            [ "data_version" => 1605
            , "error_message" =>  ""
            , "request_type" => "options"
            , "items" => $fitments
            , "parameters" =>
                [ "year" => (int) $year
                , "make" => $make
                , "model" => $model
                , "option" => $option
                ]
            , "notes" => $notes
            ];
        $this->scenario($requestUrl, $response);

        $expected = VehicleFitment::fromApiItem($standard, $notes);

        $tireSync = new TireSyncImpl($this->serviceUrl, $this->apiKey, $this->requestorIp, $this->httpClient);
        $actual = $tireSync->fetchStandardFitment($year, $make, $model, $option);

        $this->assertEquals($expected, $actual);
    }

    public function test_fetch_standard_fitment_throws_exception_when_no_standard_fitment()
    {
        $year = "2012";
        $make = "Ford";
        $model = "Crown Victoria";
        $option = "Police Interceptor";

        $notes = "Blah blah blah";

        $requestUrl = $this->serviceUrl
                    . '/v1/oe/vehicle_fitments/'
                    . $this->apiKey
                    . '/'
                    . base64_encode($this->requestorIp)
                    . '/'
                    . base64_encode($year)
                    . '/'
                    . base64_encode($make)
                    . '/'
                    . base64_encode($model)
                    . '/'
                    . base64_encode($option);

        $fitments = [];

        $response =
            [ "data_version" => 1605
            , "error_message" =>  ""
            , "request_type" => "options"
            , "items" => $fitments
            , "parameters" =>
                [ "year" => (int) $year
                , "make" => $make
                , "model" => $model
                , "option" => $option
                ]
            , "notes" => $notes
            ];
        $this->scenario($requestUrl, $response);

        $tireSync = new TireSyncImpl($this->serviceUrl, $this->apiKey, $this->requestorIp, $this->httpClient);

        try
        {
            $result = $tireSync->fetchStandardFitment($year, $make, $model, $option);
            $this->fail('fetchStandardFitment should throw a TireSyncException when no standard fitment is found.');
        }
        catch ( TireSyncException $e )
        {
            // OK!
        }
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function setUp()
    {
        $this->serviceUrl = 'http://foo.net';
        $this->apiKey = '1111.1111';
        $this->requestorIp = '127.0.0.1';
    }

    private function scenario($requestUrl, $value)
    {
        $this->httpClient = Mockery::mock(HttpClient::class, function ($mock) use ($requestUrl, $value) {
            $mock->shouldReceive('get')
                 ->once()
                 ->with($requestUrl)
                 ->andReturn($value);
        });
    }
}
