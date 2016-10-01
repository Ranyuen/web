<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Model;

use DOMDocument;
use Ranyuen\Model\Article;
use Ranyuen\Model\Photo;

class SitemapGenerator
{
    private $sitemap;
    private $urlset = array();

    public function __construct() {
        $this->sitemap                     = new DOMDocument('1.0', 'UTF-8');
        $this->sitemap->preserveWhiteSpace = false;
        $this->sitemap->formatOutput       = true;

        $this->urlset = $this->sitemap->appendChild($this->sitemap->createElement("urlset"));
        $this->urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    }

    private function add($params) {
        $url = $this->urlset->appendChild($this->sitemap->createElement('url'));
        foreach ($params as $key => $value){
            if (strlen($value)){
                $url->appendChild($this->sitemap->createElement($key, htmlentities($value)));
            }
        }
    }

    private function generate($file = null) {
        if (is_null($file)) {
            header("Content-Type: text/xml; charset=utf-8");
            echo $this->sitemap->saveXML();
        } else {
            $this->sitemap->save($file);
        }
    }

    public function articles() {
        $articles = Article::all();
        $paths = $articles->filter(function ($item) {
            if ($item->path !== '/play/exam/easy/practice'
                    && $item->path !== '/play/exam/hard/practice'
                    && $item->path !== '/play/exam/expert/practice') {

                return $item->path;
            }
        });

        return $paths;
    }

    public function generateSitemap() {
        $sitemap = new SitemapGenerator();
        $paths   = array();
        $perPage = 30;

        // Registered DB Article
        foreach ($this->articles() as $article) {
            $sitemap->add([
                'loc' => 'http://ranyuen.com' . $article->path,
                'priority' => $this->priority('http://ranyuen.com' . $article->path)
            ]);
        }

        // Photos
        $all = Photo::all()->count() % 30 === 0 ? Photo::all()->count() / 30 : ceil(Photo::all()->count() / 30);
        $calanthe = Photo::where('species_name', 'Calanthe')->count() % 30 === 0
                            ? Photo::where('species_name', 'Calanthe')->count() / 30
                            : ceil(Photo::where('species_name', 'Calanthe')->count() / 30);
        $ponerorchis = Photo::where('species_name', 'Ponerorchis')->count() % 30 === 0
                            ? Photo::where('species_name', 'Ponerorchis')->count() / 30
                            : ceil(Photo::where('species_name', 'Ponerorchis')->count() / 30);
        $nativeOrchid = Photo::where('species_name', 'Japanease native orchid')->count() % 30 === 0
                                      ? Photo::where('species_name', 'Japanease native orchid')->count() / 30
                                      : ceil(Photo::where('species_name', 'Japanease native orchid')->count() / 30);
        $others = Photo::where('species_name', NULL)->count() % 30 === 0
                            ? Photo::where('species_name', NULL)->count() / 30
                            : ceil(Photo::where('species_name', NULL)->count() / 30);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=all',
           'priority' => $this->priority('http://ranyuen.com' . $article->path)
        ]);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=Calanthe',
           'priority' => $this->priority('http://ranyuen.com' . $article->path)
        ]);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=Ponerorchis',
           'priority' => $this->priority('http://ranyuen.com' . $article->path)
        ]);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=Japanease native orchid',
           'priority' => $this->priority('http://ranyuen.com' . $article->path)
        ]);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=others',
           'priority' => $this->priority('http://ranyuen.com' . $article->path)
        ]);
        for ($i = 1; $i <= $all; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=&page=' . $i,
               'priority' => $this->priority('http://ranyuen.com' . $article->path)
            ]);
        }
        for ($i = 1; $i <= $all; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=all&page=' . $i,
               'priority' => $this->priority('http://ranyuen.com' . $article->path)
            ]);
        }
        for ($i = 1; $i <= $calanthe; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=calanthe&page=' . $i,
               'priority' => $this->priority('http://ranyuen.com' . $article->path)
            ]);
        }
        for ($i = 1; $i <= $ponerorchis; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=ponerorchis&page=' . $i,
               'priority' => $this->priority('http://ranyuen.com' . $article->path)
            ]);
        }
        for ($i = 1; $i <= $nativeOrchid; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=Japanease native orchid&page=' . $i,
               'priority' => $this->priority('http://ranyuen.com' . $article->path)
            ]);
        }
        for ($i = 1; $i <= $others; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=others&page=' . $i,
               'priority' => $this->priority('http://ranyuen.com' . $article->path)
            ]);
        }

        $sitemap->generate('sitemap.xml');
    }

    private function priority($url) {
        $last  = mb_substr($url, -1);
        $count = mb_substr_count($url, '/');

        if($last === '/') {
            if($count === 3) {
                $priority = '1.0';
            }
            else if($count === 4) {
                $priority = '0.8';
            }
            else if($count === 5) {
                $priority = '0.6';
            }
        }
        else {
            if($count === 3) {
                $priority = '0.9';
            }
            else if($count === 4) {
                $priority = '0.7';
            }
            else if($count === 5) {
                $priority = '0.5';
            }
        }

        return $priority;
    }
}
