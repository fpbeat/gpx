var mapRoute = window.mapRoute || {};

mapRoute.Base = new Class({
    Implements: Options,
    bootstraper: null,

    initialize: function (options, iOptions) {
        (iOptions !== false && typeOf(options) === 'object') && this.setOptions(options);
        this.bootstraper = new mapRoute.Bootstrap();
    },

    iGet: function () {
        var section = arguments[0].match(/^(.*?)::(.*)$/);

        if (!section) {
            throw new Error('Incorrect getter call');
        }

        switch (section[1]) {
            case 'text':
                return typeOf(arguments[1]) === 'object' ? this.options.texts[section[2]].substitute(arguments[1]) : this.options.texts[section[2]];
            case 'class':
                return this.options.classes[section[2]];
            case 'object':
                return this.options.objects[section[2]];
            case 'store':
                return this.bootstraper.store[section[2].capitalize()];
            case 'config':
                return !!section[2] !== false ? Object.getFromPath(this.bootstraper.config, section[2]) : null;
            case 'helper':
                if (typeOf(this.bootstraper.helpers[section[2]]) !== 'function') {
                    throw new Error('Helper: ' + section[2] + ' not exist');
                }
                return this.bootstraper.helpers[section[2]].apply(this.bootstraper.helpers, Array.from(arguments).slice(1).flatten());
        }
    }
});


mapRoute.Helpers = new Class({
    Extends: mapRoute.Base,
    elements: {},

    initialize: function () {
        return this;
    },

    getNumEnding: function (number, titles) {
        return titles[(number % 100 > 4 && number % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][(number % 10 < 5) ? number % 10 : 5]];
    },

    getDistance: function (distance, units) {
        var value = parseFloat(distance);

        if (isNaN(value)) {
            return '';
        }

        if (value >= 1000) {
            return Number.round(value / 1000, 1) + ' ' + units.km
        }

        return Number.round(value, 0) + ' ' + units.m;
    },

    pad: function (num, size) {
        var s = num + '';
        while (s.length < size) s = '0' + s;
        return s;
    }
});