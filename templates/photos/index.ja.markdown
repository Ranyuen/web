---
title: 写真を見る
---
蘭裕園の写真を見る
==
<?php
$controller = new \Ranyuen\Controller\ApiPhotos;
$species_name = isset($_GET['species_name']) ? $_GET['species_name'] : null;
if ($species_name === 'all') { $species_name = null; }
$photos = $controller->render('GET', [], [ 'species_name' => $species_name, 'limit' => 20 ]);
?>
<link href="/assets/bower_components/colorbox/example1/colorbox.css" rel="stylesheet" />
<script src="/assets/bower_components/colorbox/jquery.colorbox-min.js"></script>
<script src="/assets/bower_components/colorbox/i18n/jquery.colorbox-ja.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    $('.lightbox').colorbox({
      fixed:      true,
      height:     '90%',
      transition: 'fade',
      speed:      300,
      width:      '90%'
    });
  });

</script>
<style>
  .photos .photo {
    background: #f0f5f0;
    float: left;
    margin: 0.6%;
    min-height: 230px;
    width: 32%;
  }
</style>

<form method="GET">
  <select name="species_name">
    <option value="all" <?php if (!$species_name) { echo 'selected'; } ?>>全て見る</option>
    <option value="Calanthe" <?php if ($species_name === 'Calanthe') { echo 'selected'; } ?>>エビネ</option>
    <option value="Ponerorchis" <?php if ($species_name === 'Ponerorchis') { echo 'selected'; } ?>>夢チドリ/アワチドリ</option>
    <input type="submit" value="検索" />
  </select>
</form>

<div class="photos">
<?php foreach ($photos as $photo) { ?>
  <div class="photo">
    <a href="/Calanthe/gallery/<?php $h->h($photo['id']); ?>.jpg" class="lightbox" title="<?php $h->h($photo['description_ja']); ?> 蘭裕園">
      <img src="/api/photo?format=jpeg&id=<?php $h->h($photo['id']); ?>" alt="<?php $h->h($photo['description_ja']); ?> 蘭裕園"/>
    </a>
    <div>
      <div><?php $h->h($photo['description_ja']); ?></div>
      <div><?php $h->h($photo['description_en']); ?></div>
    </div>
  </div>
<?php } ?>
</div>
<!--
<script src="/assets/bower_components/masonry/dist/masonry.pkgd.min.js"></script>
<script>
new Masonry(document.getElementsByClassName('photos')[0], {
  columnWidth: '.photo',
  gutter: 0,
  itemSelector: '.photo'
});
</script>
-->
