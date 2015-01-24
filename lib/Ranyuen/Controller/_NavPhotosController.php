<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Controller;

/**
 * Static page.
 */
class _NavPhotosController extends NavController
{
    /**
     * @param string $lang Current lang
     * @param string $path URI path
     *
     * @return void
     */
    public function showFromTemplate($lang, $path = 'photos/index')
    {
        $path = 'photos/index';
        parent::showFromTemplate($lang, $path);
    }

    /**
     * Echo rendered string.
     *
     * @param string $lang         Current lang
     * @param string $templateName Template name
     * @param array  $params       Template params
     *
     * @return void
     */
    protected function render($lang, $templateName, $params = [])
    {
    }
}
