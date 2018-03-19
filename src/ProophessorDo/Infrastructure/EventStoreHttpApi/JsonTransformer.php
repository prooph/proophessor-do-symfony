<?php

declare(strict_types=1);

namespace App\ProophessorDo\Infrastructure\EventStoreHttpApi;

use Prooph\EventStore\Http\Middleware\Transformer;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

final class JsonTransformer implements Transformer
{
    public function createResponse(array $result): ResponseInterface
    {
        return new JsonResponse($result);
    }
}
