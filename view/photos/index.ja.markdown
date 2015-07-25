---
title: 写真を見る
---
蘭裕園の写真を見る
==
<form class="select" id="search-form" method="GET">
  <select id="search-form-species_name" name="species_name">
    <option value="" {% if species_name == null %}selected{% endif %}>-- 写真の種類を選ぶ --</option>
    <option value="all" {% if species_name == 'all' %}selected{% endif %}>全て見る</option>
    <option value="Calanthe" {% if species_name == 'Calanthe' %}selected{% endif %}>エビネ</option>
    <option value="Ponerorchis" {% if species_name == 'Ponerorchis' %}selected{% endif %}>アワチドリ/夢ちどり</option>
    <option value="Japanease native orchid" {% if species_name == 'Japanease native orchid' %}selected{% endif %}>日本の野生ラン</option>
    <option value="others" {% if species_name == 'others' %}selected{% endif %}>その他</option>
  </select>
</form>
<div id="photo-gallery" class="photos">
  {% for photo in photos %}
    <div class="photo">
      <a href="/images/gallery/{{ photo.id }}.jpg"
        class="lightbox"
        title="{{ photo.description_ja }} 蘭裕園 Ranyuen">
        <img rel="gallery"
          src="/api/photo?format=jpeg&id={{ photo.id }}&width={{ photo.thumb_width }}"
          width="{{ photo.thumb_width }}"
          height="{{ photo.thumb_height }}"
          alt="{{ photo.description_ja }} 蘭裕園 Ranyuen"/>
      </a>
      <div class="photo-description">
        <div>{{ photo.description_ja }}</div>
        <div>{{ photo.description_en }}</div>
      </div>
    </div>
  {% endfor %}
</div>
<link href="/assets/stylesheets/photoGallery.css" rel="stylesheet"/>
<link href="/assets/stylesheets/colorbox.css" rel="stylesheet"/>
<script src="/assets/javascripts/photoGallery.min.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    new PhotoGallery().init(document.getElementById("photo-gallery"));
    document.getElementById('search-form-species_name').onchange = function () {
      document.getElementById('search-form').submit();
    };
  });
</script>
