var Background = (function () {
    function Background() {
        var imgs = ['images/bg1.png', 'images/bg2.png'];
        this.style = 'transparent url(' + imgs[Math.floor(Math.random() * imgs.length)] + ') no-repeat scroll 200px 180px';
    }
    Background.prototype.setBackground = function (_node) {
        _node.style.background = this.style;
    };
    return Background;
})();

/**
* Nodeを組み立てるsnippet。
*/
function node(nodeName, attributes, innerHTML) {
    var attributesStr = '';
    var isEmptyNode = ['input', 'br', 'hr'].some(function (elm) {
        return elm === nodeName;
    });
    if (attributes) {
        for (var prop in attributes)
            if (attributes.hasOwnProperty(prop)) {
                attributesStr += prop + '="' + attributes[prop] + '" ';
            }
    }
    return '<' + nodeName + ' ' + attributesStr + (function () {
        if (isEmptyNode)
            return '/>';
        else
            return '>' + (innerHTML || '') + '</' + nodeName + '>';
    })();
}

function format(formatStr, values) {
    values.forEach(function (v, i) {
        formatStr = formatStr.replace('{' + i + '}', v.toString());
    });
    return formatStr;
}

// <ul>
//   <li ng-repeat="param">{{param}}</li>
// </ul>
function format2(formatStr, values) {
    var holder = document.createElement('div');
    var evaluate = function (_node) {
        if (_node instanceof Text) {
            _node.textContent = values[_node.textContent.replace(/(?:^{{)|(?:}}$)/, '')].toString();
            return;
        }
        toArray(_node.attributes).forEach(function (attribute) {
            switch (attribute.name) {
                case 'ng-repeat':
                    values[attribute.value].map(function (v) {
                    });
                    break;
                default:
                    break;
            }
        });
        toArray(_node.childNodes).forEach(function (_node) {
            return evaluate(_node);
        });
    };
    holder.innerHTML = formatStr;
    toArray(holder.childNodes).forEach(function (_node) {
        return evaluate(_node);
    });
    return holder.innerHTML;
}

// http://blog.stevenlevithan.com/archives/faster-trim-javascript
if (!String.prototype.trim) {
    String.prototype.trim = function () {
        var str = this.replace(/^\s\s*/, '');
        var ws = /\s/;
        var i = str.length;
        while (ws.test(str.charAt(i -= 1))) {
        }
        return str.slice(0, i + 1);
    };
}

function toArray(obj) {
    var result = new Array();
    for (var i = 0, iz = obj.length; i < iz; ++i)
        result.push(obj[i]);
    return result;
}

// function randamize<T>(arr: T[]): T[]
function randamize(arr) {
    var rand = function () {
        return Math.floor(Math.random() * arr.length);
    };
    var _arr = new Array();
    var i, iz;
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

function hasClass(node_, className) {
    return node_.className.match(className) ? true : false;
}

function addClass(node_, className) {
    if (!hasClass(node_, className))
        node_.className += ' ' + className;
    return node_;
}

function removeClass(node_, className) {
    node_.className = node_.className.replace(new RegExp('\\s?' + className), '');
    return node_;
}

function toggleClass(node_, className) {
    if (hasClass(node_, className))
        removeClass(node_, className);
    else
        addClass(node_, className);
    return node_;
}

function getLocationSearch(key) {
    var regex = new RegExp(key + '=([^&]+)', 'g');
    var params = new Array();
    var match;
    while (match = regex.exec(location.search)) {
        params.push(match[1]);
    }
    return params;
}

function getItselfOrDefault(expects, itself) {
    return expects.some(function (expect) {
        return expect === itself;
    }) ? itself : expects[0];
}

var messages;
var env;
function __(messageID) {
    return env && env['lang'] !== 'en' ? messages[env['lang']][messageID] : messageID;
}
// vim:set ft=typescript et sw=2 sts=2 ff=unix:
