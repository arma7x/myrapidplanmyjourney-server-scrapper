<?php
declare(strict_types=1);

use App\Application\Actions\MyRapid\V1 as MyRapidV1;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/myrapid/api/v1', function (Group $group) {
        $group->get('/list_service_msg', MyRapidV1\ListServiceMsg::class);
        $group->get('/list_service_status', MyRapidV1\ListServiceStatus::class);
        $group->get('/list_street_autocomplete', MyRapidV1\ListStreetAutocomplete::class);
        $group->get('/list_planner', MyRapidV1\ListPlanner::class);
    });
};
