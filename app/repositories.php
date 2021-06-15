<?php
declare(strict_types=1);

use App\Domain\MyRapid\MyRapidRepository;
use App\Infrastructure\Http\MyRapid\HttpMyRapidRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        MyRapidRepository::class => \DI\autowire(HttpMyRapidRepository::class),
    ]);
};
