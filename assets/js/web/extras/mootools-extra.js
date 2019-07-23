Element.implement({
    inViewport: function () {
        var rect = this.getBoundingClientRect();
        var html = document.documentElement;
        return (rect.top >= 0 && rect.left >= 0 && rect.bottom <= (window.innerHeight || html.clientHeight) && rect.right <= (window.innerWidth || html.clientWidth));
    },

    addEventGroup: function (events, callback) {
        Array.from(events).each(function (type) {
            this.addEvent(type, callback);
        });
    }
});

Options.implement({

    grabObjects: function () {
        var list = {};
        Array.from(arguments).slice(1).each(function (selector) {
            Object.append(list, selector);
        });

        var callback = (function (node, key) {
            if (typeOf(node) === 'object') {
                return Object.map(list[key], callback);
            }

            if (node !== void 0) {
                var nodeHolder = function () {
                    var pattern = node.match(/^%(\w+)/);

                    if (pattern !== null && list[pattern[1]] !== void 0) {
                        node = node.replace(new RegExp('^%' + pattern[1], 'g'), list[pattern[1]]);

                        nodeHolder();
                    }
                };

                nodeHolder();
                var collection = document.getElements(node);
                return collection.length > 1 ? collection : collection[0];
            }
        });

        Object.append(arguments[0], Object.map(list, callback));
    }
});


Object.extend({
    sum: function () {
        var sum = {};
        for (var i = 0; i < arguments.length; i++) {
            for (var j in arguments[i]) {
                if (arguments[i].hasOwnProperty(j)) {
                    sum[j] = (sum[j] || 0) + arguments[i][j];
                }
            }
        }
        return sum;
    }
});

Array.implement({
    reduce: function (fun) {
        if (this === void 0 || this === null) {
            throw TypeError();
        }

        var t = Object(this);
        var len = t.length >>> 0;
        if (typeof fun !== "function") {
            throw TypeError();
        }

        // no value to return if no initial value and an empty array
        if (len === 0 && arguments.length === 1) {
            throw TypeError();
        }

        var k = 0;
        var accumulator;
        if (arguments.length >= 2) {
            accumulator = arguments[1];
        } else {
            do {
                if (k in t) {
                    accumulator = t[k++];
                    break;
                }

                // if array contains no values, no initial value to return
                if (++k >= len) {
                    throw TypeError();
                }
            }
            while (true);
        }

        while (k < len) {
            if (k in t) {
                accumulator = fun.call(undefined, accumulator, t[k], k, t);
            }
            k++;
        }

        return accumulator;
    }
});

String.implement({
    ucfirst: function () {
        var f = this.charAt(0).toUpperCase();
        return f + this.substr(1, this.length - 1);
    },

    substitute: function (object, regexp) {
        var string = String(this).replace(/\\?#(\S+)#/g, '{$1}');

        return string.replace(regexp || (/\\?\{([^{}]+)\}/g), function (match, name) {
            if (match.charAt(0) == '\\') return match.slice(1);
            return (object[name] != null) ? object[name] : '';
        });
    }
});

Browser.Features.transitions = (function () {
    var element = (new Image).style,
        prefix = 'ransition';

    return 't' + prefix in element || 'webkitT' + prefix in element || 'MozT' + prefix in element || 'OT' + prefix in element;
})();
