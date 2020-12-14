<?php

namespace WcProToSL\Renderer;

/**
 * Class Renderer
 *
 * @package WcProToSL\Renderer
 */
class Renderer
{
    /**
     * Allows to render a view
     *
     * @param  string      $template
     * @param  array|null  $data
     *
     * @return string
     */
    public static function render(string $template, array $data = null): string
    {
        ($data) ? extract($data) : null;

        $path = self::getTemplatePath() . $template . '.php';

        if ($path) {
            ob_start();
            require($path);

            return ob_get_flush();
        }

        return false;
    }

    /**
     * Get the path of the template directory
     *
     * @return string
     */
    private static function getTemplatePath(): string
    {
        return WCPROTOSL_PATH . 'templates' . DIRECTORY_SEPARATOR;
    }
}