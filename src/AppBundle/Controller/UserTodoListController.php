<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/proophessor-do-symfony for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/proophessor-do-symfony/blob/master/LICENSE.md New BSD License
 */

declare (strict_types = 1);

namespace Prooph\AppBundle\Controller;

use Prooph\ProophessorDo\Projection\Todo\TodoFinder;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserTodoList
 *
 * @package Prooph\ProophessorDo\App\Action
 */
final class UserTodoListController
{
    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var UserFinder
     */
    private $userFinder;

    /**
     * @var TodoFinder
     */
    private $todoFinder;

    public function __construct(EngineInterface $templateEngine, UserFinder $userFinder, TodoFinder $todoFinder)
    {
        $this->templateEngine = $templateEngine;
        $this->userFinder = $userFinder;
        $this->todoFinder = $todoFinder;
    }

    public function listAction(Request $request): Response
    {
        $userId = $request->get('userId');
        $user = $this->userFinder->findById($userId);
        $todos = $this->todoFinder->findByAssigneeId($userId);

        return $this
            ->templateEngine
            ->renderResponse(
                'AppBundle:Default:user-todo-list.html.twig',
                [
                    'user' => $user,
                    'todos' => $todos
                ]
            );
    }
}
