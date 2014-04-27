---
title: 写真を見る
---
蘭裕園の写真を見る
==
<?php
$controller = new \Ranyuen\Controller\ApiPhotos;
$photos = $controller->render('GET', [], [ 'limit' => 100 ]);
?>

<style>
.photos .photo {
  background: #f0f5f0;
  float: left;
  margin: 0.6%;
  min-height: 230px;
  width: 32%;
}
</style>
<div class="photos">
<?php foreach ($photos as $photo) { ?>
  <div class="photo">
    <img src="/Calanthe/gallery/<?php $h->h($photo['id']); ?>.jpg" alt="<?php $h->h($photo['description_ja']); ?> 蘭裕園"/>
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
