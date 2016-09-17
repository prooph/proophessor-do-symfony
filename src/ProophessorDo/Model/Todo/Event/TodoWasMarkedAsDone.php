<?php
/*
 * This file is part of prooph/proophessor.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 9/15/15 - 21:20 PM
 */
namespace Prooph\ProophessorDo\Model\Todo\Event;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\Todo\TodoStatus;

/**
 * Class TodoWasMarkedAsDone
 *
 * @package Prooph\ProophessorDo\Model\Todo\Event
 * @author Danny van der Sluijs <danny.vandersluijs@icloud.com>
 */
final class TodoWasMarkedAsDone extends AggregateChanged
{
    /**
     * @var TodoId
     */
    private $todoId;

    /**
     * @var TodoStatus
     */
    private $oldStatus;

    /**
     * @var TodoStatus
     */
    private $newStatus;

    /**
     * @param TodoId $todoId
     * @param TodoStatus $oldStatus
     * @param TodoStatus $newStatus
     * @return TodoWasMarkedAsDone
     */
    public static function fromStatus(TodoId $todoId, TodoStatus $oldStatus, TodoStatus $newStatus)
    {
        $event = self::occur(
            $todoId->toString(),
            [
                'old_status' => $oldStatus->toString(),
                'new_status' => $newStatus->toString()
            ]
        );

        $event->todoId = $todoId;
        $event->oldStatus = $oldStatus;
        $event->newStatus = $newStatus;

        return $event;
    }

    /**
     * @return TodoId
     */
    public function todoId()
    {
        if ($this->todoId === null) {
            $this->todoId = TodoId::fromString($this->aggregateId());
        }

        return $this->todoId;
    }

    /**
     * @return TodoStatus
     */
    public function oldStatus()
    {
        if ($this->oldStatus === null) {
            $this->oldStatus = TodoStatus::fromString($this->payload['old_status']);
        }

        return $this->oldStatus;
    }

    /**
     * @return TodoStatus
     */
    public function newStatus()
    {
        if ($this->newStatus === null) {
            $this->newStatus = TodoStatus::fromString($this->payload['new_status']);
        }

        return $this->newStatus;
    }
}
