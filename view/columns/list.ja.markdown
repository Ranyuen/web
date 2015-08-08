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

「野生のランに魅せられて」のコラムは全500タイトル程度まで増やしていく予定でいます。まだ50タイトルと始まったばかりです。当時、私と共に行動した仲間達の記事が集まれば、国語の成績が5段階の1と2だった私とO氏の文よりはマシになると思いますのでお楽しみに。原稿待ってるよ !

コラム筆者については、[コラム筆者の紹介](columns/authors/)よりご閲覧ください。

コラムに掲載されたランも登場するランのクイズ、500問以上ありますので是非遊んでみてください。

→ <b><a href="/play/orchid_exam">ラン検定</a></b>

<div class="column" class="cf">
  <section class="column_section">
    <h1 class="column_section_title">野生のランに魅せられて</h1>
    <ol class="column_section_list">
      <li class="column_section_list_item">
        <a href="/columns/introduction_for_column">はじめに</a>
      </li>
      {% for article in articles if article.id in article.wild and article.id != 64 %}
      <li class="column_section_list_item">
        <a href="{{ article.path }}">{{ article.getContent(lang).plainTitle | raw }}</a>
      </li>
      {% endfor %}
    </ol>
  </section>
  <section class="column_section">
    <h1 class="column_section_title">夢のある農業 〜ドラマチック農業のすすめ〜</h1>
    <ol class="column_section_list">
      <li class="column_section_list_item">
        <a href="/columns/introduction_for_column_second">はじめに</a>
      </li>
      {% for article in articles if article.id in article.dream and article.id != 127 %}
      <li class="column_section_list_item">
        <a href="{{ article.path }}">{{ article.getContent(lang).plainTitle | raw }}</a>
      </li>
      {% endfor %}
    </ol>
  </section>
</div>
