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

    function __construct() {
        $this->sitemap                     = new DOMDocument('1.0', 'UTF-8');
        $this->sitemap->preserveWhiteSpace = false;
        $this->sitemap->formatOutput       = true;

        $this->urlset = $this->sitemap->appendChild($this->sitemap->createElement("urlset"));
        $this->urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    }

    function add($params) {
        $url = $this->urlset->appendChild($this->sitemap->createElement('url'));
        foreach ($params as $key => $value){
            if (strlen($value)){
                $url->appendChild($this->sitemap->createElement($key, htmlentities($value)));
            }
        }
    }

    function generate($file = null) {
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
        ]);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=Calanthe',
        ]);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=Ponerorchis',
        ]);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=Japanease native orchid',
        ]);
        $sitemap->add([
           'loc' => 'http://ranyuen.com/photos/?species_name=others',
        ]);
        for ($i = 1; $i <= $all; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=&page=' . $i,
            ]);
        }
        for ($i = 1; $i <= $all; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=all&page=' . $i,
            ]);
        }
        for ($i = 1; $i <= $calanthe; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=calanthe&page=' . $i,
            ]);
        }
        for ($i = 1; $i <= $ponerorchis; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=ponerorchis&page=' . $i,
            ]);
        }
        for ($i = 1; $i <= $nativeOrchid; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=Japanease native orchid&page=' . $i,
            ]);
        }
        for ($i = 1; $i <= $others; $i++) {
            $sitemap->add([
               'loc' => 'http://ranyuen.com/photos/?species_name=others&page=' . $i,
            ]);
        }

        $sitemap->generate('sitemap.xml');
    }
}
