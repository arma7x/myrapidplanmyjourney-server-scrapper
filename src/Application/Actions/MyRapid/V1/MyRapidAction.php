<?php
declare(strict_types=1);

namespace App\Application\Actions\MyRapid\V1;

use App\Application\Actions\Action;
use App\Domain\MyRapid\MyRapidRepository;
use Psr\Log\LoggerInterface;

abstract class MyRapidAction extends Action
{
    protected $rapidKlRepository;

    /**
     * @param LoggerInterface $logger
     * @param MyRapidRepository $rapidKlRepository
     */
    public function __construct(LoggerInterface $logger, MyRapidRepository $rapidKlRepository)
    {
        parent::__construct($logger);
        $this->rapidKlRepository = $rapidKlRepository;
    }
}
