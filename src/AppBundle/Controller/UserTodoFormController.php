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

use Prooph\ProophessorDo\Projection\User\UserFinder;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserTodoForm
 */
final class UserTodoFormController
{
    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var UserFinder
     */
    private $userFinder;

    public function __construct(EngineInterface $templateEngine, UserFinder $userFinder)
    {
        $this->templateEngine  = $templateEngine;
        $this->userFinder = $userFinder;
    }

    public function formAction(Request $request): Response
    {
        $userId = $request->get('user_id');

        $invalidUser = true;
        $user = null;

        if ($userId) {
            $user = $this->userFinder->findById($userId);

            if ($user) {
                $invalidUser = false;
            }
        }

        return $this->templateEngine->renderResponse(
            'AppBundle:Default:user-todo-form.html.twig',
            ['invalidUser' => $invalidUser, 'user' => $user]
        );
    }
}
