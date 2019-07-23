var mapRoute = mapRoute || {};

mapRoute.Bootstrap = new Class({
    Extends: mapRoute.Base,

    store: {},
    config: {},
    helpers: null,

    initialize: function () {
        this.helpers = new mapRoute.Helpers();

        this.before.call(this);
        window.addEvent('domready', this.after);

        return this;
    },

    before: function () {
        Locale.use('ru-RU');

        // Redefine months
        Locale.define('ru-RU', 'Date', 'months', ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря']);
    },

    after: function () {
        // nope
    }
});