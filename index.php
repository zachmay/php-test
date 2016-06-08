<?php

require_once 'vendor/autoload.php';

error_reporting(E_ALL);

use Slim\App;

$config =
    [ 'settings' =>
        [ 'displayErrorDetails' => true
        ]
    , 'view' =>
        function ($container) {
            $view = new \Slim\Views\Twig('templates');
            $view->addExtension(new \Slim\Views\TwigExtension(
                $container['router'],
                $container['request']->getUri()
            ));
            return $view;
        }
    , 'http' =>
        function ($container) {
            return new \Uparts\Http\JsonHttpClient();
        }
    , 'tireSyncServiceUrl' => 'http://api.tiresync.com'
    , 'tireSyncApiKey' => '1111-1111-1111-1111'
    , 'tireSync' =>
        function ($container) {
            $requestorIp = $container['request']->getServerParams()['REMOTE_ADDR'];
            $tireSyncServiceUrl = $container['tireSyncServiceUrl'];
            $tireSyncApiKey = $container['tireSyncApiKey'];
            $httpClient = $container['http'];

            return new \Uparts\TireSync\TireSyncImpl($tireSyncServiceUrl, $tireSyncApiKey, $requestorIp, $httpClient);
        }
    ];

$app = new App($config);

$container = $app->getContainer();


$app->get('/', '\Uparts\Handlers\FitmentsHandler:get');

$app->run();

/*
use Uparts\TireSync\TireSyncImpl;
use Uparts\Http\JsonHttpClient;

$httpClient = new JsonHttpClient();
$tireSync = new TireSyncImpl('http://api.tiresync.com', '1111-1111-1111-1111', 'localhost', $httpClient);

var_dump($tireSync->fetchAvailableYears());
 */
