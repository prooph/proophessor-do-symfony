<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\ProophessorDo\Projection\User;

use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasReopened;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasUnmarkedAsExpired;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;

/**
 * Class UserProjection
 * @package Prooph\ProophessorDo\Projection\User
 */
final class UserProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                UserWasRegistered::class => function ($state, UserWasRegistered $event) {
                    /** @var UserReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('insert', [
                        'id' => $event->userId()->toString(),
                        'name' => $event->name()->toString(),
                        'email' => $event->emailAddress()->toString(),
                    ]);
                },
                TodoWasPosted::class => function ($state, TodoWasPosted $event) {
                    /** @var UserReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('postTodo', $event->assigneeId()->toString());
                },
                TodoWasMarkedAsDone::class => function ($state, TodoWasMarkedAsDone $event) {
                    /** @var UserReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('markTodoAsDone', $event->assigneeId()->toString());
                },
                TodoWasReopened::class => function ($state, TodoWasReopened $event) {
                    /** @var UserReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('reopenTodo', $event->assigneeId()->toString());
                },
                TodoWasMarkedAsExpired::class => function ($state, TodoWasMarkedAsExpired $event) {
                    /** @var UserReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('markTodoAsExpired', $event->assigneeId()->toString());
                },
                TodoWasUnmarkedAsExpired::class => function ($state, TodoWasUnmarkedAsExpired $event) {
                    /** @var UserReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('unmarkTodoAsExpired', $event->assigneeId()->toString());
                },
            ]);

        return $projector;
    }
}
