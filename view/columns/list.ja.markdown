---
title: コラム
description:
---
<link rel="stylesheet" href="/assets/stylesheets/news_column.css" />
<link rel="stylesheet" href="/assets/stylesheets/news.css" />
コラム <small>- 野生のランに魅せられて -</small>
==

<figure>
{{ '7e2d852c-7a30-4b53-ac9c-6335c4af21be' | echoImg('100%', 'auto') | raw}}
</figure>

このコラムページは日本の野生ランに魅せられた若者たちが、自然の中に自生するランを探し歩く中でおきた一連のでき事をつづった物語です。全て実体験を元に書きつづったものですが、30 ~ 40年前の体験記のため写真のないものもあります。
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
    	<h1><a href="{{ article.path }}">{{ article.getContent(lang).plainTitle | raw }}</a></h1>
    </article>
  {% endfor %}
</div>
