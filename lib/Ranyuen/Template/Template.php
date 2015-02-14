<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Template;

/**
 * Template interface.
 */
interface Template
{
    /**
     * Register the helper.
     *
     * @param mixed $helper Template helper
     *
     * @return void
     */
    public function registerHelper($helper);

    /**
     * Parses the given source string.
     *
     * @param string $template Template string
     *
     * @return Template
     */
    public function parse($template);

    /**
     * Renders the current template.
     *
     * @param array $params     Template params
     * @param mixed $tmpHelpers Temporary helpers
     *
     * @return string
     */
    public function render(array $params = [], $tmpHelpers = null);
}
