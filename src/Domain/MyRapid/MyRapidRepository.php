<?php
declare(strict_types=1);

namespace App\Domain\MyRapid;

interface MyRapidRepository
{
    public function ListServiceMsg(array $query): array;
    public function ListServiceStatus(array $query): array;
    public function ListStreetAutocomplete(array $query): array;
    public function ListPlanner(array $query): array;
}
