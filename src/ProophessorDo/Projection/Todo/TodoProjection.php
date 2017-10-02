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

namespace Prooph\ProophessorDo\Projection\Todo;

use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;
use Prooph\ProophessorDo\Model\Todo\Event\DeadlineWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\ReminderWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsExpired;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasReopened;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasUnmarkedAsExpired;

final class TodoProjection implements ReadModelProjection
{

    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                TodoWasPosted::class => function ($state, TodoWasPosted $event) {
                    /** @var TodoReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('insert', [
                        'id' => $event->todoId()->toString(),
                        'assignee_id' => $event->assigneeId()->toString(),
                        'text' => $event->text()->toString(),
                        'status' => $event->todoStatus()->toString(),
                    ]);
                },
                TodoWasMarkedAsDone::class => function ($state, TodoWasMarkedAsDone $event) {
                    /** @var TodoReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'update',
                        [
                            'status' => $event->newStatus()->toString(),
                        ],
                        [
                            'id' => $event->todoId()->toString(),
                        ]
                    );
                },
                TodoWasReopened::class => function ($state, TodoWasReopened $event) {
                    /** @var TodoReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'update',
                        [
                            'status' => $event->status()->toString(),
                        ],
                        [
                            'id' => $event->todoId()->toString(),
                        ]
                    );
                },
                DeadlineWasAddedToTodo::class => function ($state, DeadlineWasAddedToTodo $event) {
                    /** @var TodoReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'update',
                        [
                            'deadline' => $event->deadline()->toString(),
                        ],
                        [
                            'id' => $event->todoId()->toString(),
                        ]
                    );
                },
                ReminderWasAddedToTodo::class => function ($state, ReminderWasAddedToTodo $event) {
                    /** @var TodoReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'update',
                        [
                            'reminder' => $event->reminder()->toString(),
                        ],
                        [
                            'id' => $event->todoId()->toString(),
                        ]
                    );
                },
                TodoWasMarkedAsExpired::class => function ($state, TodoWasMarkedAsExpired $event) {
                    /** @var TodoReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'update',
                        [
                            'status' => $event->newStatus()->toString(),
                        ],
                        [
                            'id' => $event->todoId()->toString(),
                        ]
                    );
                },
                TodoWasUnmarkedAsExpired::class => function ($state, TodoWasUnmarkedAsExpired $event) {
                    /** @var TodoReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'update',
                        [
                            'status' => $event->newStatus()->toString(),
                        ],
                        [
                            'id' => $event->todoId()->toString(),
                        ]
                    );
                },
            ]);

        return $projector;
    }
}
