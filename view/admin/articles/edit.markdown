---
title: Edit article
---
<link rel="stylesheet" href="/assets/stylesheets/article_editor.css"/>
<div class="articleEditor">
  <div class="articleEditor_path">
    <label for="articleEditor_path_input">URLパス</label>
    <input id="articleEditor_path_input" class="articleEditor_path_input" type="text" size="80" placeholder="/admin/articles/edit"/>
  </div>
  <div class="articleEditor_langTab">
    <div class="articleEditor_langTab_plus">+</div>
  </div>
  <div class="articleEditor_content">
    <textarea class="articleEditor_content_input" cols="80" rows="42"></textarea>
    <div class="articleEditor_content_preview">{{preview}}</div>
  </div>
  <ul class="articleEditor_errors"></ul>
  <button class="articleEditor_save">保存</button>
  <button class="articleEditor_remove">削除</button>
</div>
<template id="articleEditor_langTab_item">
  <div class="articleEditor_langTab_item">
    <div class="articleEditor_langTab_item_lang">{{lang}}</div>
    <div class="articleEditor_langTab_item_remove">×</div>
  </div>
</template>
<template id="articleEditor_errors_error">
  <li class="articleEditor_errors_error">{{message}}</li>
</template>
<script src="/assets/javascripts/article_editor.min.js"></script>
<script>
var editor,
    article = '{{article}}';
article = article.
  replace(/\n/g,     '\\n').
  replace(/\r/g,     '\\r').
  replace(/&lt;/g,   '<').
  replace(/&gt;/g,   '>').
  replace(/&quot;/g, '"').
  replace(/&#039;/g, "'").
  replace(/&amp;/g,  '&');
article = JSON.parse(article);
article = article ? Article.fromJson(article) : new Article();
window.addEventListener('DOMContentLoaded', function () {
  editor = new ArticleEditor(document.querySelector('.articleEditor'), article);
  editor.run();
});
</script>
