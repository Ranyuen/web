---
title: Edit news
---
<form action="/admin/news/update/{{ article.id }}" method="POST">
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
    <input value="Update" type="submit"/>
  </div>
  <input type="hidden" name="_METHOD" value="PUT"/>
</form>
<form action="/admin/news/destroy/{{ article.id }}" method="POST">
  <div>
    <input value="Delete" type="submit"/>
  </div>
  <input type="hidden" name="_METHOD" value="DELETE"/>
</form>
