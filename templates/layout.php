<?php
$site_names = ['ja' => '蘭裕園', 'en' => 'Ranyuen'];
$site_name = $site_names[$lang];
$home = "http://ranyuen.com{$link['base']}";
$local_base = preg_replace('/\/[^\/]*$/', '/', $_SERVER['REQUEST_URI']);
$switch_lang = [];
foreach ([
    'en' => 'English',
    'ja' => '日本語',
] as $k => $v) {
    $switch_lang[] =  $lang === $k ? $v : "<a href=\"{$link[$k]}\">$v</a>";
}
$switch_lang = implode(' / ', $switch_lang);
?>
<!DOCTYPE html>
<!--[if lt IE 9]><html class="ie"><![endif]-->
<!--[if gte IE 9]><!--><html><!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <title><?php $h->h("$title - $site_name"); ?></title>
    <meta name="google-site-verification" content="osPpSihI4cWmpC3IfcU0MFq6zDdSPWyb7V2_ECHHo5Q"/>
    <meta name="msvalidate.01" content="C6AA98E0859490689AD2DDDC23486114"/>
<?php if (isset($description)) { ?>
    <meta name="description" content="<?php $h->h($description); ?>"/>
<?php } ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="home" href="<?php $h->h($home); ?>"/>
    <base href="<?php $h->h($link['base']); ?>"/>
    <link rel="author" href="https://plus.google.com/117493105665785554638?rel=author"/>
    <link rel="stylesheet" href="/assets/bower_components/normalize-css/normalize.css"/>
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Alef:400,700" type="text/css"/>
    <link rel="stylesheet" href="/assets/stylesheets/layout.css"/>
    <style>
    body, .header {
        background: url('<?php $h->h($bgimage); ?>') fixed;
    }
    </style>
    <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="/assets/bower_components/uri.js/src/URI.js"></script>
    <script src="/assets/bower_components/zepto/zepto.min.js"></script>
    <script src="/assets/bower_components/bluebird/js/browser/bluebird.js"></script>
    <script src="/assets/bower_components/platform/platform.js"></script>
    <link rel="import" href="/assets/bower_components/polymer/polymer.html"/>
</head>
<body class="<?php $h->h($lang); ?>" lang="<?php $h->h($lang); ?>">
    <header>
        <nav class="global-nav">
            <div class="nav-left">
                <a rel="home" class="logo" href="<?php $h->h($home); ?>" title="<?php $h->h($site_name); ?>">
                    <img src="/assets/images/icons/ranyuen.png" alt="<?php $h->h($site_name); ?>" longdesc="<?php $h->h($home); ?>"/>
                </a>
                <div class="logo-span nav-item"> </div>
            <?php $h->echoNav($global_nav, $link['base']); ?>
            </div>
            <div class="nav-right">
                <div class="lang"><?php echo $switch_lang; ?></div>
            </div>
        </nav>
        <nav class="local-nav">
        <?php $h->echoNav($local_nav, $local_base); ?>
        </nav>
    </header>
    <div class="container">
        <!-- <div id="indicate">0</div>
        <script>
        var t = 0;
        function rfrsh()
        {
            if (t % 10 === 0) {
                t = 0;
                document.getElementById('indicate').textContent =
                    window.getComputedStyle(document.querySelector('.container')).width + '/' + window.getComputedStyle(document.body).width;
            }
            ++ t;
            requestAnimationFrame(rfrsh);
        }
        requestAnimationFrame(rfrsh);
        </script> -->
        <article>
            <header>
                <!-- <div class="aside">
                    <div class="news">
                        <img src="/assets/images/icons/feed.png"
                             width="30" height="30"/>NEWS
                    <?php $h->echoNav($news_nav, "{$link['base']}/news/"); ?>
                    </div>
                    <a href="https://www.facebook.com/ranyuenjapan"
                       target="_blank"
                       class="facebook"
                       title="Ranyuen Japan Facebook Page">
                        <img src="/assets/images/icons/facebook.png"
                             alt="Ranyuen Japan Facebook Page"
                             longdesc="https://www.facebook.com/ranyuenjapan"/>
                        <div style="line-height:13pt;">Ranyuen<br/>Facebook</div>
                    </a>
                </div> -->
                <?php $h->echoBreadcrumb($breadcrumb, $link['base']); ?>
            </header>
            <article><?php $h->render($content, $__params); ?></article>
        </article>
        <footer>
            <nav><?php $h->echoNav($global_nav, $link['base']); ?></nav>
            <p class="copyright">Copyright (C) 2010-2014 <a rel="home" href="<?php $h->h($home); ?>">Ranyuen</a> All Rights Reserved.<br/>
            Spring Calanthe (EBINE) and Ponerorchis (AWACHIDORI &amp; YUMECHIDORI) you see on our website are all bred, researched and developed in our <a rel="home" href="<?php $h->h($home); ?>">Ranyuen</a>&#39;s farm.</p>
        </footer>
    </div>
    <script>
    window.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.container').style.top = window.getComputedStyle(document.querySelector('body > header')).height;
    });
    </script>
    <script src="/assets/javascripts/messageForDeveloperFromRanyuen.js"></script>
    <script>
        (function (i,s,o,g,r,a,m) {i['GoogleAnalyticsObject']=r;i[r]=i[r]||function () {
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-47871400-1', 'ranyuen.com');
        ga('send', 'pageview');
    </script>
</body>
</html>
