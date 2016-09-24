<?php

use Ranyuen\Model\SitemapGenerator;
use Ranyuen\Model\Article;
use Ranyuen\Model\Photo;

use Illuminate\Database\Capsule\Manager as DB;

class SitemapTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->configureDatabase();
    }

    protected function configureDatabase() {
        $db = new DB;
        $db->addConnection(array(
            'host'      => 'localhost',
            'driver'    => 'mysql',
            'database'  => 'ranyuen_production',
            'username'  => 'root',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ));
        $db->bootEloquent();
        $db->setAsGlobal();
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

    public function testGenerateSitemap() {
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
