---
title: コラム
description:
---
<link rel="stylesheet" href="/assets/stylesheets/news_column.css" />
コラム
==
このコラムページは日本の野生ランに魅せられた若者たちの体験記です。全て実体験を元に書きつづったものですが、30 ~ 40年前の体験記のため写真のないものもあります。
ご了承ください。

コラムに掲載されたランも登場するランクイズあります! 500問近くありますので是非遊んでみてください。<br /> → [ラン検定](play/orchid_exam)

<div class="column">
  {% for article in articles %}
    <article class="column-article">
    	<h1><a href="/{{ article.lang }}/news/{{ article.url }}">{{ article.plainTitle | raw }}</a></h1>
    </article>
  {% endfor %}
</div>
