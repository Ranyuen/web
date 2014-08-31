<?php
$site_names = ['ja' => 'エビネとウチョウランの蘭裕園', 'en' => 'Ranyuen'];
$site_name = $site_names[$lang];
$site_keywords = ['ja' => 'エビネ, ウチョウラン', 'en' => 'Calanthe, Ponerorchis'];
$site_keyword = $site_keywords[$lang];
$site_catchCopy = 'エビネとウチョウランの専門農園';
$home = "http://ranyuen.com{$link['base']}"; 
$local_base = preg_replace('/\/[^\/]*$/', '/', $_SERVER['REQUEST_URI']);
$switch_lang = [];
foreach (['en' => 'English', 'ja' => '日本語'] as $k => $v) {
  $switch_lang[] =  $lang === $k ? $v : "<a href=\"{$link[$k]}\">$v</a>";
}
$switch_lang = implode(' / ', $switch_lang);
?>
<!DOCTYPE html>
<!--[if lt IE 9]><html class="ie"><![endif]-->
<!--[if gte IE 9]><!--><html><!--<![endif]-->
<head>
  <meta charset="UTF-8">
  <title><?php if($title === '蘭裕園') { $h -> h("$site_name - $site_catchCopy"); } else { $h -> h("$title | $site_name"); } ?></title>
  <meta name="google-site-verification" content="osPpSihI4cWmpC3IfcU0MFq6zDdSPWyb7V2_ECHHo5Q"/>
  <meta name="msvalidate.01" content="C6AA98E0859490689AD2DDDC23486114"/>
  <?php if (isset($description)) { ?>
  <meta name="description" content="<?php $h -> h($description); ?>"/> <?php } ?>
  <meta name="keywords" content="<?php $h -> h($site_keyword); ?>"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link rel="home" href="<?php $h -> h($home); ?>"/>
  <base href="<?php $h -> h($link['base']); ?>"/>
  <link rel="author" href="https://plus.google.com/117493105665785554638?rel=author"/>
  <link rel="stylesheet" href="/assets/bower_components/normalize-css/normalize.css"/>
  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Alef:400,700" type="text/css"/>
  <link rel="stylesheet" href="/assets/stylesheets/layout.css"/>
  <style>
    body, .header {
      background: url('<?php $h -> h($bgimage); ?>') fixed;
    }
  </style>
  <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script src="/assets/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="/assets/bower_components/uri.js/src/URI.js"></script>
</head>
<body class="<?php $h -> h($lang); ?>" lang="<?php $h -> h($lang); ?>">
  <div class="container">
    <header class="cf">
      <div class="headerInner cf">
        <div class="logo">
          <h1>
            <a rel="home" href="<?php $h -> h($home); ?>">
              <img src="/assets/images/icons/ranyuen.png" alt="<?php $h -> h("$site_name - $site_catchCopy"); ?>" longdesc="<?php $h -> h($home); ?>"/>
            </a>
          </h1>
        </div><!-- /logo -->
        <div class="lang">
          <?php echo $switch_lang; ?>
        </div><!-- /lang -->
      </div><!-- /headerInner -->
      <nav class="cf">
        <?php $h -> echoNav($global_nav, $link['base']); ?>
      </nav>
    </header>
    <div class="main cf">
      <div class="mainInner">
        <?php $h -> echoBreadcrumb($breadcrumb, $link['base']); ?>
        <article>
          <?php $h -> render($content, $__params); ?>
        </article>
      </div><!-- /mainInner -->
      <div class="side">
        <aside>
          <?php $h -> echoNav($local_nav, $local_base); ?>
        </aside>
      </div><!-- /side -->
    </div><!-- /main -->
    <footer class="cf">
      <p class="copyright">
        <small>
          Copyright (C) 2010-2014 <a rel="home" href="<?php $h -> h($home); ?>"><?php $h -> h("$site_name - $site_catchCopy"); ?></a> All Rights Reserved.<br/>
          Spring Calanthe (EBINE) and Ponerorchis (AWACHIDORI &amp; YUMECHIDORI) you see on our website are all bred, researched and developed in our <a rel="home" href="<?php $h -> h($home); ?>">Ranyuen</a>&#39;s farm.
        </small>
      </p>
    </footer>
  </div>
<script src="/assets/javascripts/messageForDeveloperFromRanyuen.js"></script>
<script>
  (function(i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] ||
    function() {
      (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o), m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
  })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

  ga('create', 'UA-47871400-1', 'ranyuen.com');
  ga('send', 'pageview');
</script>
</body>
</html>
