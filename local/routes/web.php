<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Dev\Corporate\Controller\CorporateTaxi;

return static function (RoutingConfigurator $routes) {
    $routes->get('/corporate/taxi/free', [CorporateTaxi::class, 'index']);
};
