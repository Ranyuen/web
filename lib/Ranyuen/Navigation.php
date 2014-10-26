<?php
namespace Ranyuen;

class Navigation
{
    private $config;
    private $nav;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->nav = simplexml_load_file('config/nav.xml');
    }

    /**
     * @param  string $lang
     * @return array
     */
    public function getGlobalNav($lang)
    {
        $lang = htmlspecialchars($lang, ENT_QUOTES, 'utf-8');

        return $this->gather($this->nav->xpath("/nav/lang[@name='$lang']")[0]);
    }

    /**
     * @return string[]
     */
    public function getLangs()
    {
        $langs = [];
        foreach ($this->nav->lang as $elm) {
            $langs[] = (string) $elm['name'];
        }
        $langs = array_unique(array_merge($langs, array_keys($this->config['lang'])));

        return $langs;
    }

    /**
     * @param  string $lang
     * @return array
     */
    public function getNews($lang)
    {
        $lang = htmlspecialchars($lang, ENT_QUOTES, 'utf-8');
        $nav = $this->nav->xpath("/nav/lang[@name='$lang']/dir[@path='news']");
        $news = $nav ? $this->gather($nav[0]) : [];
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
        $lang = htmlspecialchars($lang, ENT_QUOTES, 'utf-8');
        $nav = $this->nav->xpath("/nav/lang[@name='$lang']")[0];
        foreach ($template_name as $part) {
            $part = htmlspecialchars($part, ENT_QUOTES, 'utf-8');
            if ($part && $part !== 'index') {
                if (!$nav->xpath("*[@path='$part']") ||
                    $nav->xpath("*[@path='$part']")[0]->getName() === 'page') {
                    break;
                }
                $nav = $nav->xpath("*[@path='$part']")[0];
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
        $lang = htmlspecialchars($lang, ENT_QUOTES, 'utf-8');
        $nav = $this->nav->xpath("/nav/lang[@name='$lang']")[0];
        $path = '/';
        $breadcrumb = [$path => (string) $nav->page[0]['title']];
        foreach (explode('/', $template_name) as $part) {
            $part = htmlspecialchars($part, ENT_QUOTES, 'utf-8');
            if ($part === 'index') {
                break;
            }
            if ($nav->xpath("*[@path='$part']")) {
                $path .= $part . '/';
                $nav = $nav->xpath("*[@path='$part']")[0];
            } else {
                break;
            }
            if ($nav->xpath("*[@path='index']")) {
                $breadcrumb[$path] = (string) $nav->xpath("*[@path='index']")[0]['title'];
            } elseif ($nav->getName() === 'page') {
                $breadcrumb[$path] = (string) $nav['title'];
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
        $alter['local_base'] = preg_replace(
            '/\/[^\/]*$/',
            '/',
            $_SERVER['REQUEST_URI']
        );
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
        foreach ($nav->children() as $elm) {
            if ($elm->getName() === 'page') {
                if ((string) $elm['path'] === 'index') {
                    $index['/'] = (string) $elm['title'];
                } else {
                    $local[(string) $elm['path']] = (string) $elm['title'];
                }
            } else {
                if ($elm->xpath("page[@path='index']")) {
                    $sub[(string) $elm['path'] . '/'] = (string) $elm->xpath("page[@path='index']")[0]['title'];
                }
            }
        }
        $local = array_merge($index, $local);
        $local = array_merge($local, $sub);

        return $local;
    }
}
