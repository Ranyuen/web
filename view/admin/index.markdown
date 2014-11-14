---
title: Admin
---
Admin
==
News
--
[New news](/admin/news/new)

{% for article in articles %}
1. [{{ article.title }}](/admin/news/edit/{{ article.id }})
{% endfor %}

News tag
--
[New news tag](/admin/news_tag/new)

{% for article_tag in article_tags %}
1. [{{ article_tag.name_ja }}/{{ article_tag.name_en }}](/admin/news_tag/edit/{{ article_tag.id }})
{% endfor %}
