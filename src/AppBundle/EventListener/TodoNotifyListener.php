<?php

namespace Prooph\AppBundle\EventListener;

use Prooph\ProophessorDo\Model\Todo\Command\MarkTodoAsExpired;
use Prooph\ProophessorDo\Model\Todo\Command\RemindTodoAssignee;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\Todo\TodoReminder;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class TodoNotifyListener
 *
 * @author Patrick Blom <info@patrick-blom.de>
 */
final class TodoNotifyListener implements EventSubscriberInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var TodoFinder
     */
    private $todoFinder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TodoNotifyListener constructor.
     * @param CommandBus $commandBus
     * @param TodoFinder $todoFinder
     * @param LoggerInterface $logger
     */
    public function __construct(CommandBus $commandBus, TodoFinder $todoFinder, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->todoFinder = $todoFinder;
        $this->logger = $logger;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onTerminate(PostResponseEvent $event)
    {
        $this->onTerminateSendReminderNotifications($event);
        $this->onTerminateSendExpiredNotifications($event);
    }

    /**
     * @param PostResponseEvent $event
     */
    private function onTerminateSendExpiredNotifications(PostResponseEvent $event)
    {
        $expiredTodos = $this->todoFinder->findOpenWithPastTheirDeadline();

        if (0 === count($expiredTodos)) {
            $this->logger->debug('no expired todos found, exit process');
            return;
        }

        $this->logger->debug(
            sprintf(
                '%s expired todos found, start dispatching commands',
                count($expiredTodos)
            )
        );

        /** @var \stdClass $todo */
        foreach ($expiredTodos as $todo) {
            if (isset($todo->id)) {
                $this->logger->debug(
                    sprintf(
                        'dispatching the MarkTodoAsExpired command for todo %s with topic %s',
                        $todo->id,
                        $todo->text
                    )
                );

                try {
                    $this->commandBus->dispatch(
                        MarkTodoAsExpired::forTodo($todo->id)
                    );
                } catch (CommandDispatchException $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }

    /**
     * @param PostResponseEvent $event
     */
    private function onTerminateSendReminderNotifications(PostResponseEvent $event)
    {
        $todosToRemind = $this->todoFinder->findByOpenReminders();

        if (0 === count($todosToRemind)) {
            $this->logger->debug('no todos found for the reminding process, exit process');
            return;
        }

        $this->logger->debug(
            sprintf(
                '%s todos found that have to be reminded, start dispatching commands',
                count($todosToRemind)
            )
        );

        /** @var \stdClass $todo */
        foreach ($todosToRemind as $todo) {
            if (isset($todo->id)) {
                $this->logger->debug(
                    sprintf(
                        'dispatching the RemindTodoAssignee command for todo %s with topic %s',
                        $todo->id,
                        $todo->text
                    )
                );

                try {
                    $command = RemindTodoAssignee::forTodo(
                        TodoId::fromString($todo->id),
                        TodoReminder::fromString($todo->reminder, $todo->status)
                    );
                    $this->commandBus->dispatch($command);
                } catch (CommandDispatchException $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }


    /**
     * @{@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => 'onTerminate'
        ];
    }
}