<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\View;

use LPTS\Infrastructure\Plugin;

/**
 * @since 1.1.8
 */
class Renderer
{
    private Plugin $plugin;

    public function __construct()
    {
        $this->plugin = Plugin::getInstance();
    }

    /**
     * Render a view with parameters.
     *
     * This method renders a view file with the given parameters.
     *
     * @param string $view The view file to display (without the .php extension).
     * @param array<string, mixed> $params The parameters to extract into the view.
     *
     * @return string The rendered HTML content of the view.
     *
     * @throws \RuntimeException If the view file does not exist.
     */
    public function render(string $view, array $params = []): string
    {
        return $this->processRender($view, $params);
    }

    /**
     * AJAX render a view with parameters.
     *
     * This method is similar to `render`, but it inserts the result into the `$data` array under the `html` key.
     *
     * @param string $view The view file to display (without the .php extension).
     * @param array<string, mixed> &$data The data array where the rendered HTML will be inserted.
     * @param array<string, mixed> $params The parameters to extract into the view.
     *
     * @return void
     *
     * @throws \RuntimeException If the view file does not exist.
     */
    public function ajaxRender(string $view, array &$data, array $params = []): void
    {
        $data['html'] = $this->processRender($view, $params, $data);
    }

    /**
     * Internal method to render a view.
     *
     * This method is used by both `render` and `ajaxRender` to generate the HTML content of a template.
     *
     * @param string $view The view file to display (without the .php extension).
     * @param array<string, mixed> $params The parameters to extract into the view.
     *
     * @return string|false The rendered HTML content of the view, or false on failure.
     *
     * @throws \RuntimeException If the view file does not exist.
     */
    private function processRender(string $view, array $params = [], array &$data = []): false|string
    {
        $path = $this->plugin->getTemplatePath() . DIRECTORY_SEPARATOR . $view . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException(
            // Translators: %s is the path of the template.
                \sprintf(
                    __('The file %s does not exist', 'link-products-to-sendinblue'),
                    $path
                )
            );
        }

        ob_start();

        // Include the $data array in the extracted parameters.
        $params['data'] = &$data;

        // EXTR_SKIP prevents overwriting existing variables, while EXTR_OVERWRITE allows it.
        // Basically, if we already have a variable $foo = 'bar' and we pass $params = array( 'foo' => 'baz' )
        // then with EXTR_SKIP we will have $foo = 'bar' and with EXTR_OVERWRITE we will have $foo = 'baz'.
        extract($params, EXTR_SKIP);
        include $path;

        $buffer = ob_get_clean();

        // if (is_admin()) {
        //     $buffer = ob_get_contents();
        // }

        return $buffer;
    }
}
