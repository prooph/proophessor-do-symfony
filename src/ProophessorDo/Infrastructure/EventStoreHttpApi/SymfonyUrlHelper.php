<?php

declare(strict_types=1);

namespace App\ProophessorDo\Infrastructure\EventStoreHttpApi;

use Prooph\EventStore\Http\Middleware\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SymfonyUrlHelper implements UrlHelper
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generate(string $urlId, array $params = []): string
    {
        return $this->urlGenerator->generate($urlId, $params);
    }
}
