<?php
namespace Ranyuen\Template;

interface Template
{
    /**
     * Register the helper.
     *
     * @param mixed $helper
     *
     * @return void
     */
    public function registerHelper($helper);

    /**
     * Parses the given source string.
     *
     * @param string $template
     *
     * @return Template
     */
    public function parse($template);

    /**
     * Renders the current template.
     *
     * @param array $__params
     * @param mixed $tmp_helpers
     *
     * @return string
     */
    public function render(array $__params = [], $tmp_helpers = null);
}
