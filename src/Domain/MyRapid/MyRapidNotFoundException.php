<?php
declare(strict_types=1);

namespace App\Domain\MyRapid;

use App\Domain\DomainException\DomainRecordNotFoundException;

class MyRapidNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The query you requested does not exist.';
}
