---
title: コラム
description:
---
<link rel="stylesheet" href="/assets/stylesheets/news_column.css" />
<link rel="stylesheet" href="/assets/stylesheets/news.css" />
コラム
==
このコラムページは日本の野生ランに魅せられた若者たちの体験記です。全て実体験を元に書きつづったものですが、30 ~ 40年前の体験記のため写真のないものもあります。
ご了承ください。

コラム筆者については、[コラム筆者の紹介](columns/authors/)よりご閲覧ください。

<div class="article-box">
<p>コラムに掲載されたランも登場するランのクイズあります!</p>
<p>500問近くありますので是非遊んでみてください。</p>
<p>→ <b><a href="/play/orchid_exam">ラン検定</a></b></p>
</div>

<div class="column">
  {% for article in articles %}
    <article class="column-article">
    	<h1><a href="/{{ article.lang }}/news/{{ article.url }}">{{ article.plainTitle | raw }}</a></h1>
    </article>
  {% endfor %}
</div>
