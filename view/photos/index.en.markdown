---
title: Photos
---
Photos in Ranyuen
==
<form id="search-form" method="GET">
  <select id="search-form-species_name" name="species_name">
    <option value="" {% if species_name == null %}selected{% endif %}>--Select species--</option>
    <option value="all" {% if species_name == 'all' %}selected{% endif %}>All</option>
    <option value="Calanthe" {% if species_name == 'Calanthe' %}selected{% endif %}>Calanthe</option>
    <option value="Ponerorchis" {% if species_name == 'Ponerorchis' %}selected{% endif %}>Ponerorchis</option>
    <option value="Japanease native orchid" {% if species_name == 'Japanease native orchid' %}selected{% endif %}>Japanese native orchids</option>
    <option value="others" {% if species_name == 'others' %}selected{% endif %}>Others</option>
  </select>
</form>
<div id="photo-gallery" class="photos">
  {% for photo in photos %}
    <div class="photo">
      <a href="/images/gallery/{{ photo.id }}.jpg"
        class="lightbox"
        title="{{ photo.description_en }} Ranyuen">
        <img rel="gallery"
        src="/api/photo?format=jpeg&id={{ photo.id }}&width={{ photo.thumb_width }}"
        width="{{ photo.thumb_width }}"
        height="{{ photo.thumb_height }}"
        alt="{{ photo.description_en }} Ranyuen"/>
      </a>
      <div class="photo-description">
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
