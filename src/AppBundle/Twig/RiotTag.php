<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/proophessor-do-symfony for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/proophessor-do-symfony/blob/master/LICENSE.md New BSD License
 */

declare (strict_types = 1);

namespace Prooph\AppBundle\Twig;

class RiotTag extends \Twig_Extension
{
    private $search = ['"', PHP_EOL];

    private $replace = ['\"', ''];

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'riotTag',
                [$this, 'render'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true // Tell twig we need the environment
                ]
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'prooph:riot-tag';
    }

    public function render(\Twig_Environment $twig, $tagName, $template = null, $jsFunction = null)
    {
        if ($template === null) {
            $template = $tagName;
            $tagName = $this->getTagNameFromTemplate($template);
        }

        $this->assertTagName($tagName);
        $this->assertTemplate($template);

        $template = $twig->render($template);

        if ($jsFunction === null) {
            $jsFunction = $this->extractJsFunction($template, $tagName);
            $template = $this->removeJsFromTemplate($template, $tagName);
        }

        return 'riot.tag("' . $tagName . '", "' . str_replace($this->search, $this->replace,
            $template) . '", ' . $jsFunction . ');';
    }

    private function getTagNameFromTemplate($template)
    {
        $this->assertTemplate($template);

        $startPos = strpos($template, 'riot-');
        $endPos = strpos($template, '.html.twig');

        return substr($template, $startPos + 5, $endPos - $startPos - 5);
    }

    private function assertTagName($tagName)
    {
        if (!is_string($tagName)) {
            throw new \InvalidArgumentException('Riot tag name should be a string. got ' . gettype($tagName));
        }
    }

    private function assertTemplate($template)
    {
        if (!is_string($template)) {
            throw new \InvalidArgumentException('Riot template should be a string. got ' . gettype($template));
        }
    }

    private function extractJsFunction($template, $tagName)
    {
        preg_match(
            '/<script .*type="text\/javascript"[^>]*>[\s]*(?<func>function.+\});?[\s]*<\/script>/is',
            $template,
            $matches
        );

        if (!$matches['func']) {
            throw new \RuntimeException('Riot tag javascript function could not be found for tag name: ' . $tagName);
        }

        return $matches['func'];
    }

    private function removeJsFromTemplate($template, $tagName)
    {
        $template = preg_replace('/<script .*type="text\/javascript"[^>]*>.*<\/script>/is', '', $template);

        if (!$template) {
            throw new \RuntimeException('Riot tag template compilation failed for tag: ' . $tagName . ' with error code: ' . preg_last_error());
        }

        return $template;
    }
}
