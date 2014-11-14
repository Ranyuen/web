---
title: New news
---
<form action="/admin/news/create" method="POST">
  <div>
    <label for="news_title">Title</label>
    <input id="news_title" name="title" value="{{ article.title }}" type="text" required size="80"/>
  </div>
  <div>
    <label for="news_url">URL</label>
    <input id="news_url" name="url" value="{{ article.url }}" type="text" required size="80"/>
  </div>
  <div>
    <label for="news_lang">lang</label>
    <input id="news_lang" name="lang" value="{{ article.lang }}" type="text" required pattern="[a-z]{2,3}" size="3"/>
  </div>
  <div>
    <label for="news_description">Description</label>
    <textarea id="news_description" name="description" type="text" cols="80" rows="2">{{ article.description }}</textarea>
  </div>
  <div>
  <div>
    <label for="news_content">Content</label>
    <textarea id="news_content" name="content" required cols="80" rows="30">{{ article.content }}</textarea>
  </div>
  <div>
    <label for="news_tags">Tags</label>
    <input id="news_tags" name="tags" type="text" value="{% for tag in article.tags %}{{ tag.name_en }},{% endfor %}" multiple list="list_tags" required size="80"/>
  </div>
  <div>
    <input value="Create" type="submit"/>
  </div>
</form>
<datalist id="list_tags">
{% for tag in tags %}
  <option value="{{ tag.name_en }}"/>
{% endfor %}
</datalist>
