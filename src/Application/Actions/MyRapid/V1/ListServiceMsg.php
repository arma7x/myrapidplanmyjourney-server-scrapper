<?php
declare(strict_types=1);

namespace App\Application\Actions\MyRapid\V1;

use Psr\Http\Message\ResponseInterface as Response;

class ListServiceMsg extends MyRapidAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->respondWithData($this->rapidKlRepository->ListServiceMsg($this->request->getQueryParams()));
    }
}
