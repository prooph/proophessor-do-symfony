<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/proophessor-do-symfony for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/proophessor-do-symfony/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace App\Controller;

use Prooph\Common\Messaging\MessageFactory;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApiCommandController
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    const NAME_ATTRIBUTE = 'prooph_command_name';

    public function __construct(CommandBus $commandBus, MessageFactory $messageFactory, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->messageFactory = $messageFactory;
        $this->logger = $logger;
    }

    public function postAction(Request $request)
    {
        $commandName = $request->attributes->get(self::NAME_ATTRIBUTE);

        if (null === $commandName) {
            return JsonResponse::create(
                [
                    'message' => sprintf(
                        'Command name attribute ("%s") was not found in request.',
                        self::NAME_ATTRIBUTE
                    )
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        try {
            $payload = $this->getPayloadFromRequest($request);
        } catch (\Throwable $error) {
            return JsonResponse::create(
                [
                    'message' => $error->getMessage()
                ],
                $error->getCode()
            );
        }

        $command = $this->messageFactory->createMessageFromArray($commandName, ['payload' => $payload]);

        try {
            $this->commandBus->dispatch($command);
        } catch (CommandDispatchException $ex) {
            $this->logger->error($ex->getPrevious());
            return JsonResponse::create(
                ['message' => $ex->getPrevious()->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Throwable $error) {
            $this->logger->error($error);
            return JsonResponse::create(['message' => $error->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return JsonResponse::create(null, Response::HTTP_ACCEPTED);
    }

    private function getPayloadFromRequest(Request $request): array
    {
        $payload = json_decode($request->getContent(), true);

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new \Exception('Invalid JSON, maximum stack depth exceeded.', 400);
            case JSON_ERROR_UTF8:
                throw new \Exception('Malformed UTF-8 characters, possibly incorrectly encoded.', 400);
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_STATE_MISMATCH:
                throw new \Exception('Invalid JSON.', 400);
        }

        return $payload === null ? [] : $payload;
    }
}
