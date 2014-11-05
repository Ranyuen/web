---
title: Admin Login
---
{{ title }}
==
<form action="/admin/login" method="POST">
  <div>
    <input name="username" value="{{ username }}" placeholder="User name" type="text" size="40"/>
  </div>
  <div>
    <input name="password" value="{{ password }}" placeholder="Password" type="password" size="40"/>
  </div>
  <div>
    <input value="Login" type="submit"/>
  </div>
</form>
