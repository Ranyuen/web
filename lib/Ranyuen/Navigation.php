<?php
namespace Ranyuen;

use \Symfony\Component\Yaml\Yaml;

class Navigation
{
    private $config;
    private $nav;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->nav = Yaml::parse(file_get_contents("{$config['templates.path']}/nav.json"));
    }

    /**
     * @param  string $lang
     * @return array
     */
    public function getGlobalNav($lang)
    {
        return $this->gather($this->nav[$lang]);
    }

    /**
     * @return string[]
     */
    public function getLangs()
    {
        $langs = array_keys($this->nav);
        $langs = array_unique(array_merge($langs, array_keys($this->config['lang'])));

        return $langs;
    }

    /**
     * @param  string $lang
     * @return array
     */
    public function getNews($lang)
    {
        $news = isset($this->nav[$lang]['news']) ?
            $this->gather($this->nav[$lang]['news']) :
            [];
        unset($news['index']);

        return $news;
    }

    /**
     * @param  string $lang
     * @param  string $template_name
     * @return array
     */
    public function getLocalNav($lang, $template_name)
    {
        $template_name = explode('/', $template_name);
        $nav = $this->nav[$lang];
        foreach ($template_name as $part) {
            if ($part && $part !== 'index') {
                if (!isset($nav[$part]) || isset($nav[$part]['title'])) { break; }
                $nav = $nav[$part];
            }
        }

        return $this->gather($nav);
    }

    /**
     * @param  string $lang
     * @param  string $template_name
     * @return array
     */
    public function getBreadcrumb($lang, $template_name)
    {
        $nav = $this->nav[$lang];
        $path = '/';
        $breadcrumb = [$path => $nav['index']['title']];
        foreach (explode('/', $template_name) as $part) {
            if ($part === 'index') { break; }
            if (isset($nav[$part])) {
                $path .= $part . '/';
                $nav = $nav[$part];
            } else {
                break;
            }
            if (isset($nav['index'])) {
                $breadcrumb[$path] = $nav['index']['title'];
            } else if (isset($nav['title'])) {
                $breadcrumb[$path] = $nav['title'];
            }
        }

        return $breadcrumb;
    }

    /**
     * @param  string $lang
     * @param  string $template_name
     * @return array
     */
    public function getAlterNav($lang, $template_name)
    {
        $dir = dirname("{$this->config['templates.path']}/$template_name");
        $alt_lang = [];
        if (! is_dir($dir)) { $dir = "{$this->config['templates.path']}/"; }
        if ($handle = opendir($dir)) {
            $regex = '/^(?:' . basename($template_name) . ')\.(\w+)\.\w+$/';
            while (false !== ($file = readdir($handle))) {
                $matches = [];
                if (is_file("$dir/$file") && preg_match($regex, $file, $matches)) {
                    $alt_lang[] = $matches[1];
                }
            }
        }
        $alter = [];
        $link_data = [
            'ja' => '/',
            'en' => '/en/',
        ];
        $alter['base'] = $link_data[$lang];
        foreach ($link_data as $k => $v) {
            $alter[$k] = $v;
            if (false !== array_search($k, $alt_lang)) {
                $t = preg_replace('/index$/', '', $template_name);
                $alter[$k] = preg_replace('/\/\//', '/', $v . $t);
            }
        }

        return $alter;
    }

    private function gather($nav)
    {
        $index = [];
        $local = [];
        $sub = [];
        foreach ($nav as $href => $meta) {
            if (isset($meta['title'])) {
                $index[$href === 'index' ? '/' : $href] = $meta['title'];
            } else {
                $sub["$href/"] = isset($nav[$href]['index']) ?
                    $nav[$href]['index']['title'] :
                    null;
            }
        }
        $local = array_merge($index, $local);
        $local = array_merge($local, $sub);

        return $local;
    }
}
