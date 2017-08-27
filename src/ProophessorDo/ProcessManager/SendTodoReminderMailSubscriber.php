<?php

namespace Prooph\ProophessorDo\ProcessManager;

use Prooph\ProophessorDo\Model\Todo\Event\TodoAssigneeWasReminded;
use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use Psr\Log\LoggerInterface;

/**
 * Class SendTodoReminderMailSubscriber
 *
 * @package Prooph\ProophessorDo\App\Mail
 * @author Roman Sachse <r.sachse@ipark-media.de>, Patrick Blom <info@patrick-blom.de>
 */
final class SendTodoReminderMailSubscriber
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
     * @param UserFinder $userFinder
     * @param TodoFinder $todoFinder
     * @param \Swift_Mailer $mailer
     */
    public function __construct(UserFinder $userFinder, TodoFinder $todoFinder, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $this->userFinder = $userFinder;
        $this->todoFinder = $todoFinder;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @param TodoAssigneeWasReminded $event
     */
    public function onTodoAssigneeWasReminded(TodoAssigneeWasReminded $event)
    {
        $user = $this->userFinder->findById($event->userId()->toString());
        $todo = $this->todoFinder->findById($event->todoId()->toString());

        $messageBody = sprintf(
            "Hi %s!  This a reminder for your todo `%s` . Don't be lazy!.",
            $user->name,
            $todo->text
        );

        $message = \Swift_Message::newInstance()
            ->setSubject('Proophessor-do Todo Reminder')
            ->setFrom('reminder@getprooph.org', 'Proophessor-do')
            ->setTo($user->email, $user->name)
            ->setBody($messageBody);

        $this->mailer->send($message);

        $this->logger->debug('mail was sent to ' . $user->email);
    }
}
