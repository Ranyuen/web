class Background {
  style: string;

  constructor() {
    var imgs = ['images/bg1.png', 'images/bg2.png'];
    this.style = 'transparent url(' + imgs[Math.floor(Math.random() * imgs.length)] + ') no-repeat scroll 200px 180px';
  }

  setBackground(_node: HTMLElement): void {
    _node.style.background = this.style;
  }
}

/**
 * Nodeを組み立てるsnippet。
 */
function node(nodeName: string, attributes?: any, innerHTML?: string): string {
  var attributesStr = '';
  var isEmptyNode = ['input', 'br', 'hr'].some((elm) => elm === nodeName);
  if (attributes) {
    for (var prop in attributes) if (attributes.hasOwnProperty(prop)) {
      attributesStr += prop + '="' + attributes[prop] + '" ';
    }
  }
  return '<' + nodeName + ' ' + attributesStr +
    (() => {
      if (isEmptyNode)
        return '/>';
      else
        return '>' + (innerHTML || '') + '</' + nodeName + '>';
    })();
}

function format(formatStr: string, values: any[]): string {
  values.forEach((v, i) => {
    formatStr = formatStr.replace('{' + i + '}', v.toString());
  });
  return formatStr;
}

// <ul>
//   <li ng-repeat="param">{{param}}</li>
// </ul>
function format2(formatStr: string, values: any): string {
  var holder = document.createElement('div');
  var evaluate = (_node: HTMLElement): void => {
    if (_node instanceof Text) {
    _node.textContent = values[_node.textContent.replace(/(?:^{{)|(?:}}$)/, '')].toString();
      return;
    }
    toArray(_node.attributes).forEach((attribute) => {
      switch (attribute.name) {
        case 'ng-repeat':
          values[attribute.value].map((v) => {
          });
          break;
        default:
          break;
      }
    });
    toArray(_node.childNodes).forEach((_node) => evaluate(_node));
  };
  holder.innerHTML = formatStr;
  toArray(holder.childNodes).forEach((_node) => evaluate(_node));
  return holder.innerHTML;
}

// http://blog.stevenlevithan.com/archives/faster-trim-javascript
if (!String.prototype.trim) {
  String.prototype.trim = function(): string {
    var str = this.replace(/^\s\s*/, '');
    var ws = /\s/;
    var i = str.length;
    while (ws.test(str.charAt(i -= 1))) {
    }
    return str.slice(0, i + 1);
  };
}

function toArray(obj: any): any[] {
  var result = new Array<any>();
  for (var i = 0, iz = obj.length; i < iz; ++i)
    result.push(obj[i]);
  return result;
}

// function randamize<T>(arr: T[]): T[]
function randamize(arr: any[]): any[] {
  var rand = (): number => Math.floor(Math.random() * arr.length);
  var _arr = new Array<any>();
  var i, iz: number;
  for (i = 0, iz = arr.length; i < iz; ++i)
    _arr[i] = arr[i];
  for (i = 0, iz = _arr.length; i < iz; ++i) {
    var j1 = rand(), j2 = rand();
    var tmp = _arr[j1];
    _arr[j1] = _arr[j2];
    _arr[j2] = tmp;
  }
  return _arr;
}

function hasClass(node_: HTMLElement, className: string): boolean {
  return node_.className.match(className) ? true : false;
}

function addClass(node_: HTMLElement, className: string): HTMLElement {
  if (!hasClass(node_, className)) node_.className += ' ' + className;
  return node_;
}

function removeClass(node_: HTMLElement, className: string): HTMLElement {
  node_.className = node_.className.replace(new RegExp('\\s?' + className), '');
  return node_;
}

function toggleClass(node_: HTMLElement, className: string): HTMLElement {
  if (hasClass(node_, className)) removeClass(node_, className);
  else addClass(node_, className);
  return node_;
}

function getLocationSearch(key: string): string[] {
  var regex = new RegExp(key + '=([^&]+)', 'g');
  var params = new Array<string>();
  var match: any;
  while (match = regex.exec(location.search)) {
    params.push(match[1]);
  }
  return params;
}

function getItselfOrDefault(expects: string[], itself: string): string;
function getItselfOrDefault(expects: any[], itself: any): any {
  return expects.some((expect) => expect === itself) ? itself : expects[0];
}

var messages: any;
var env: { lang: string; };
function __(messageID: string): string {
  return env && env['lang'] !== 'en' ? messages[env['lang']][messageID] : messageID;
}
// vim:set ft=typescript et sw=2 sts=2 ff=unix:
