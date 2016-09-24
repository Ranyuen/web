<?php
require 'vendor/autoload.php';

use Ranyuen\Model\SitemapGenerator;

(new Ranyuen\App())->container['db'];

$sitemap = new SitemapGenerator;
$sitemap->generateSitemap();
