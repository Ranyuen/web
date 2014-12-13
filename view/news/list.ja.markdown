---
title: コラム
description:
---
<link rel="stylesheet" href="/assets/stylesheets/news_column.css">
コラム
==
<div class="column">
  {% for article in articles %}
    <article class="column-article">
    	<h1><a href="/{{ article.lang }}/news/{{ article.url }}">{{ article.title | raw }}</a></h1>
    </article>
  {% endfor %}
</div>
