<?php

/**
 * Ranyuen web site.
 */

namespace Ranyuen\Template;

use Ranyuen\BgImage;
use Ranyuen\Navigation\Navigation;

/**
 */
class MainViewRenderer
{
    private $renderer;
    private $nav;
    private $bgimage;
    private $config;

    public function __construct(ViewRenderer $renderer, Navigation $nav, BgImage $bgimage, array $config)
    {
        $this->renderer = $renderer;
        $this->nav      = $nav;
        $this->bgimage  = $bgimage;
        $this->config   = $config;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->renderer, $name], $arguments);
    }

    /**
     * @param string $lang Current lang.
     * @param string $path URI path info.
     *
     * @return array
     */
    public function defaultParams($lang, $path)
    {
        if (isset($this->config['lang'][$lang])) {
            $lang = $this->config['lang'][$lang];
        }

        return [
            'lang'       => $lang,
            'localnav'   => $this->nav->getLocalNav($lang, $path),
            'breadcrumb' => $this->nav->getBreadcrumb($lang, $path),
            'link'       => $this->getLinks($lang, $path),
            'bgimage'    => $this->bgimage->getRandom(),
            'messages'   => $this->config['message'][$lang],
        ];
    }

    /**
     * 日本語版 / 英語版の対応をとります。.
     *
     * @param string $lang Current lang.
     * @param string $path URI path info.
     *
     * @return array
     */
    private function getLinks($lang, $path)
    {
        $dir = dirname("{$this->config['templates.path']}/$path");
        $altLang = [];
        if (! is_dir($dir)) {
            $dir = "{$this->config['templates.path']}/";
        }
        if ($handle = opendir($dir)) {
            $regex = '/^(?:'.basename($path).')\.(\w+)\.\w+$/';
            while (false !== ($file = readdir($handle))) {
                $matches = [];
                if (is_file("$dir/$file") && preg_match($regex, $file, $matches)) {
                    $altLang[] = $matches[1];
                }
            }
        }
        $alter = [];
        $linkData = [
            'ja' => '/',
            'en' => '/en/',
        ];
        $alter['base'] = $linkData[$lang];
        $alter['local_base'] = preg_replace('/\/[^\/]*$/', '/', $path);
        foreach ($linkData as $k => $v) {
            $alter[$k] = $v;
            if (false !== array_search($k, $altLang)) {
                $t = preg_replace('/index$/', '', $path);
                $alter[$k] = preg_replace('/\/\//', '/', $v.$t);
            }
        }

        return $alter;
    }
}
