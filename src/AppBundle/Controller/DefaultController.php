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

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    /**
     * @var EngineInterface
     */
    private $templateEngine;

    public function __construct(EngineInterface $engine)
    {
        $this->templateEngine = $engine;
    }

    public function indexAction(Request $request): Response
    {
        return $this
            ->templateEngine
            ->renderResponse(
                'AppBundle:Default:index.html.twig',
                ['sidebar_right' => '']
            );
    }
}
