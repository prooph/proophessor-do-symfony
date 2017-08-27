<?php

namespace Prooph\ProophessorDo\ProcessManager;

use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use Psr\Log\LoggerInterface;

/**
 * Class SendTodoDeadlineExpiredMailSubscriber
 *
 * @package Prooph\ProophessorDo\App\Mail
 * @author Michał Żukowski <michal@durooil.com
 */
final class SendTodoDeadlineExpiredMailSubscriber
{
    /**
     * @var UserFinder
     */
    private $userFinder;

    /**
     * @var TodoFinder
     */
    private $todoFinder;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SendTodoDeadlineExpiredMailSubscriber constructor.
     * @param UserFinder $userFinder
     * @param TodoFinder $todoFinder
     * @param \Swift_Mailer $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(UserFinder $userFinder, TodoFinder $todoFinder, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $this->userFinder = $userFinder;
        $this->todoFinder = $todoFinder;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @param TodoWasMarkedAsExpired $event
     * @return void
     */
    public function onTodoWasMarkedAsExpired(TodoWasMarkedAsExpired $event)
    {
        $todo = $this->todoFinder->findById($event->todoId()->toString());
        $user = $this->userFinder->findById($todo->assignee_id);
        $messageBody = sprintf(
            'Hi %s! Just a heads up: your todo `%s` has expired on %s.',
            $user->name,
            $todo->text,
            $todo->deadline
        );

        $message = \Swift_Message::newInstance()
            ->setSubject('Proophessor-do Todo expired')
            ->setFrom('reminder@getprooph.org', 'Proophessor-do')
            ->setTo($user->email, $user->name)
            ->setBody($messageBody);

        $this->mailer->send($message);

        $this->logger->debug('mail was sent to ' . $user->email);
    }
}
