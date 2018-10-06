/* global Promise, global, throttle */
(function (global) {
/* jshint browser:true, maxstatements:1000 */
'use strict';

function fromTemplate(id) {
  return document.importNode(document.getElementById(id).content, true).firstElementChild;
}

// {{{ EventRouter
var EventRouter = {
  listeners : {},

  emit : function (name, params, me) {
    params = params || [];
    me     = me     || null;
    this.listeners[name].forEach(function (listener) {
      listener.apply(me, params);
    });
  },

  on : function (name, listener) {
    if (!this.listeners[name]) {
      this.listeners[name] = [];
    }
    this.listeners[name].push(listener);
  }
};
// }}} EventRouter

// {{{ ArticleEditor
/**
 * @class
 *
 * @prop {Article}        article
 * @prop {ArticleContent} currentContent
 * @prop {LangTabItem[]}  langTabItems
 * @prop {HTMLElement}    node
 */
function ArticleEditor(node, article) {
  /* jshint maxstatements:100 */
  var me = this;
  if (ArticleEditor.instance) {
    return ArticleEditor.instance;
  }
  ArticleEditor.instance = this;
  article = article || new Article();
  this.article            = null;
  this.currentContent     = null;
  this.langTabItems       = [];
  this.node               = node;
  this.nodePathInput      = node.querySelector('.articleEditor_path_input');
  this.nodeLangTab        = node.querySelector('.articleEditor_langTab');
  this.nodeLangTabPlus    = node.querySelector('.articleEditor_langTab_plus');
  this.nodeContentInput   = node.querySelector('.articleEditor_content_input');
  this.nodeContentPreview = node.querySelector('.articleEditor_content_preview');
  this.nodeErrors         = node.querySelector('.articleEditor_errors');
  this.nodeSave           = node.querySelector('.articleEditor_save');
  this.nodeRemove         = node.querySelector('.articleEditor_remove');
  EventRouter.on('selectLangTabItem', function (langTabItem, content) {
    me.switchContent(langTabItem, content);
  });
  EventRouter.on('changeLangTabItemLang', function (langTabItem, content) {
    content.lang = langTabItem.nodeLang.textContent;
  });
  EventRouter.on('removeLangTabItem', function (langTabItem, content) {
    me.removeContent(langTabItem, content);
  });
  this.nodePathInput.addEventListener('keyup', function () {
    me.article.path = me.nodePathInput.value;
  });
  this.nodeLangTabPlus.addEventListener('click', function () {
    me.createContent();
  });
  this.nodeSave.addEventListener('click', function () {
    me.save();
  });
  this.nodeRemove.addEventListener('click', function () {
    me.destroy();
  });
  this.attachArticle(article);
}

ArticleEditor.instance = null;

ArticleEditor.prototype.run = function () {
  var me                 = this,
      lastPreviewContent = '';
  requestAnimationFrame(function preview() {
    me.currentContent.content = me.nodeContentInput.value;
    if (lastPreviewContent !== me.currentContent.content) {
      me.preview(function (err) {
        if (err) {
          return me.showError(err);
        }
        lastPreviewContent = me.currentContent.content;
      });
    }
    requestAnimationFrame(preview);
  });
};

ArticleEditor.prototype.switchContent = function (langTabItem, content) {
  this.currentContent = content;
  this.langTabItems.forEach(function (langTabItem) {
    langTabItem.unselect();
  });
  langTabItem.select();
  this.nodeContentInput.value = this.currentContent.content;
  this.preview();
};

ArticleEditor.prototype.createContent = function (content) {
  var langTabItem;
  if (!content) {
    content = new ArticleContent();
    this.article.contents.push(content);
  }
  langTabItem = new LangTabItem(content);
  this.langTabItems.push(langTabItem);
  this.nodeLangTab.insertBefore(langTabItem.node, this.nodeLangTabPlus);
  this.switchContent(langTabItem, content);
};

ArticleEditor.prototype.removeContent = function (langTabItem, content) {
  /* jshint maxstatements:100 */
  var nextLangTabItem,
      i  = 0,
      iz = 0;
  if (!window.confirm('ArticleContent ' + content.lang + ' を削除するか?')) {
    return;
  }
  for (i = 0, iz = this.article.contents.length; i < iz; ++i) {
    if (content === this.article.contents[i]) {
      this.article.contents.splice(i, 1);
      break;
    }
  }
  for (i = 0, iz = this.langTabItems.length; i < iz; ++i) {
    if (langTabItem === this.langTabItems[i]) {
      this.langTabItems.splice(i, 1);
      break;
    }
  }
  if (langTabItem.node.classList.contains('articleEditor_langTab_item-selected')) {
    if (0 === this.langTabItems.length) {
      this.createContent();
      nextLangTabItem = this.langTabItems[0];
    } else if (0 === i) {
      nextLangTabItem = this.langTabItems[0];
    } else {
      nextLangTabItem = this.langTabItems[i - 1];
    }
    this.switchContent(nextLangTabItem, nextLangTabItem.content);
  }
  this.nodeLangTab.removeChild(langTabItem.node);
};

ArticleEditor.prototype.preview = throttle(function (done) {
  var me = this;
  this.currentContent.render().then(function (html) {
    var sandbox  = document.createElement('div'),
        fragment = document.createDocumentFragment();
    sandbox.innerHTML = html;
    Array.from(sandbox.childNodes).forEach(function (child) {
      fragment.appendChild(child);
    });
    me.nodeContentPreview.innerHTML = '';
    me.nodeContentPreview.appendChild(fragment);
    if (done) {
      done();
    }
  }).catch(function (err) {
    if (done) {
      done(err);
    }
  });
}, 500);

ArticleEditor.prototype.save = throttle(function () {
  var me = this;
  this.nodeErrors.innerHTML = '';
  return this.article.save().then(function () {
    console.log('Last saved at ' + new Date().toISOString());
    if (/\/0$/.test(location.href)) {
      history.pushState({}, document.title, location.href.replace(/0$/, me.article.id));
    }
    me.nodeSave.textContent = '保存済み';
    setTimeout(function () {
      me.nodeSave.textContent = '保存';
    }, 2000);
  }).catch(function (err) {
    me.showError(err);
  });
}, 2000);
window.addEventListener('popstate', function (evt) {
  if (!evt.state) {
    return;
  }
  history.bask();
});

ArticleEditor.prototype.destroy = function () {
  var me = this;
  if (!window.confirm('Articleを削除するか?')) {
    return;
  }
  this.article.destroy().then(function () {
    location.href = '/admin/';
  }).catch(function (err) {
    me.showError(err);
  });
};

ArticleEditor.prototype.showError = function (err) {
  var node;
  if (err instanceof ValidationError) {
    this.nodeErrors.appendChild(err.messages.reduce(function (fragment, message) {
      var node = fromTemplate('articleEditor_errors_error');
      node.textContent = message;
      fragment.appendChild(node);
      return fragment;
    }, document.createDocumentFragment()));
    return;
  }
  node = fromTemplate('articleEditor_errors_error');
  node.textContent = err.message;
  this.nodeErrors.appendChild(node);
};

ArticleEditor.prototype.attachArticle = function (article) {
  this.article = article;
  this.nodePathInput.value = article.path;
  article.contents.forEach(function (content) {
    this.createContent(content);
  }, this);
  this.switchContent(this.langTabItems[0], this.langTabItems[0].content);
};
// }}} ArticleEditor

// {{{ LangTabItem
/**
 * @class
 */
function LangTabItem(content) {
  var me = this;
  this.content    = content;
  this.isSelected = false;
  this.node       = fromTemplate('articleEditor_langTab_item');
  this.nodeLang   = this.node.querySelector('.articleEditor_langTab_item_lang');
  this.nodeRemove = this.node.querySelector('.articleEditor_langTab_item_remove');
  this.nodeLang.textContent = content.lang || '-';
  this.nodeLang.addEventListener('click', function () {
    EventRouter.emit('selectLangTabItem', [me, me.content]);
  });
  this.nodeLang.addEventListener('keyup', function () {
    EventRouter.emit('changeLangTabItemLang', [me, content]);
  });
  this.nodeRemove.addEventListener('click', function () {
    EventRouter.emit('removeLangTabItem', [me, content]);
  });
}

LangTabItem.prototype.select = function () {
  if (this.isSelected) {
    return;
  }
  this.isSelected = true;
  this.node.classList.add('articleEditor_langTab_item-selected');
  this.nodeLang.contentEditable = true;
};

LangTabItem.prototype.unselect = function () {
  if (!this.isSelected) {
    return;
  }
  this.isSelected = false;
  this.node.classList.remove('articleEditor_langTab_item-selected');
  this.nodeLang.contentEditable = false;
  EventRouter.emit('changeLangTabItemLang', [this, this.content]);
};
// }}} LangTabItem

// {{{ Article
/**
 * Entity.
 *
 * @class
 *
 * @prop {string}           path
 * @prop {ArticleContent[]} contents
 */
function Article() {
  var content;
  this.id       = 0;
  this.path     = '';
  this.contents = [];
  this.errors   = [];
  content = new ArticleContent();
  content.lang = 'ja';
  this.contents.push(content);
  content = new ArticleContent();
  content.lang = 'en';
  this.contents.push(content);
}

Article.fromJson = function (json) {
  var article = new Article();
  if ('string' === typeof json || json instanceof String) {
    json = JSON.parse(json);
  }
  article.id       = json.id;
  article.path     = json.path;
  article.contents = json.contents.reduce(function (contents, content) {
    contents.push(ArticleContent.fromJson(content));
    return contents;
  }, []);
  return article;
};

Article.prototype.isValid = function () {
  this.errors = [];
  if (!this.path) {
    this.errors.push('Article#pathは必須');
  }
  if (!this.path.startsWith('/')) {
    this.errors.push('Article#pathはルート/から始まらねばならない: ' + this.path);
  }
  if (0 === this.contents.length) {
    this.errors.push('Article#contentsは必須');
  }
  this.errors = this.errors.concat(this.contents.reduce(function (errors, content) {
    if (!content.isValid()) {
      errors = errors.concat(content.errors);
    }
    return errors;
  }, []));
  return 0 === this.errors.length;
};

Article.prototype.toJson = function () {
  return JSON.stringify({
    id       : this.id,
    path     : this.path,
    contents : this.contents.map(function (content) {
      return {
        lang    : content.lang,
        content : content.content
      };
    })
  });
};

Article.prototype.save = function () {
  var me  = this,
      req = new XMLHttpRequest();
  req.open('PUT', '/admin/articles/update/' + this.id);
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
  return new Promise(function (resolve, reject) {
    if (!me.isValid()) {
      return reject(new ValidationError(me.errors));
    }
    req.onload = function () {
      me.id = JSON.parse(req.responseText).id;
      resolve();
    };
    req.onerror = function () {
      if (422 === req.status) {
        return reject(new ValidationError([JSON.parse(req.statusText).errors]));
      }
      reject(new NetworkError(req));
    };
    req.send('article=' + encodeURIComponent(me.toJson()));
  });
};

Article.prototype.destroy = function () {
  var req = new XMLHttpRequest();
  req.open('DELETE', '/admin/articles/destroy/' + this.id);
  return new Promise(function (resolve, reject) {
    req.onload = function () {
      resolve();
    };
    req.onerror = function () {
      reject(new NetworkError(req));
    };
    req.send();
  });
};

Article.prototype.findByLang = function (lang) {
  return this.contents.filter(function (content) {
    return content.lang === lang;
  })[0];
};
// }}} Article

// {{{ ArticleContent
/**
 * Entity.
 *
 * @class
 *
 * @prop {string} lang
 * @prop {string} content
 */
function ArticleContent() {
  this.lang    = '';
  this.content = '---\ntitle: 題\ndescription: 説明\n---\n{{ title }}\n==\n本文。\n\n----\n\n → [関連コラム](/)\n\nイラスト：xxxx\n\nコラム筆者：[xxxx](/)\n\n[「xxxx」へ戻る](/)\n\n[ホームへ戻る](/)';
  this.errors  = [];
}

ArticleContent.fromJson = function (json) {
  var content = new ArticleContent();
  if ('string' === typeof json || json instanceof String) {
    json = JSON.parse(json);
  }
  content.lang    = json.lang;
  content.content = json.content;
  return content;
};

ArticleContent.prototype.isValid = function () {
  this.errors = [];
  if (!this.lang) {
    this.errors.push('ArticleContent#langは必須');
  }
  if (!/^[a-z]{2,3}$/.test(this.lang)) {
    this.errors.push('ArticleContent#langはISO 3166-1形式でなければならない: ' + this.lang);
  }
  return 0 === this.errors.length;
};

ArticleContent.prototype.render = function () {
  var me  = this,
      req = new XMLHttpRequest();
  req.open('POST', '/admin/articles/preview');
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
  return new Promise(function (resolve, reject) {
    req.onload = function () {
      resolve(req.responseText);
    };
    req.onerror = function () {
      reject(new NetworkError(req));
    };
    req.send('content=' + encodeURIComponent(me.content));
  });
};
// }}} ArticleContent

// {{{ ValidationError
function ValidationError(messages) {
  this.name     = ValidationError.name;
  this.messages = messages;
  this.stack    = new Error().stack;
}

ValidationError.prototype = Object.create(Error.prototype, {
  constructor : { value : ValidationError }
});
// }}}

// {{{ NetworkError
function NetworkError(req) {
  var message;
  this.name = NetworkError.name;
  message = (req.responseText || this.name) + ' HTTP status: ' + req.status;
  this.message = message;
  this.stack   = new Error().stack;
}

NetworkError.prototype = Object.create(Error.prototype, {
  constructor : { value : NetworkError }
});
// }}}

global.ArticleEditor  = ArticleEditor;
global.Article        = Article;
global.ArticleContent = ArticleContent;
}((this || 0).self || global));
// vim:fdm=marker:
