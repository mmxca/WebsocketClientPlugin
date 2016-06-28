<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'WebSocketClientPlugin',
    ['path' => '/websocket-client-plugin'],
    function (RouteBuilder $routes) {
        $routes->fallbacks('DashedRoute');
    }
);
