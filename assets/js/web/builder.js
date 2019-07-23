var mapRoute = window.mapRoute || {};

mapRoute.Builder = new Class({
    Extends: mapRoute.Base,
    Implements: Events,

    options: {
        objects: {},
        map: {
            center: [56.833333, 60.583333],
            minRouteSnapDistance: 500,
            zoom: 10
        },
        settings: {
            mapApiKey: null,
            mapWidth: 500,
            mapHeight: 200,
            mapType: 'yandex#satellite',
            mapRouteColor: '#E5751E',
            mapPlacemarks: true,
            mapPlacemarkColor: 'blueDotIcon',
            mapHint: true,
            mapHintFields: ['time', 'distance', 'ele'],
            chartWidth: 300,
            chartHeight: 200,
            chartName: null,
            chartLineColor: '#434348',
            chartBaseColor: '#90ed7d',
            chartZoom: true
        },
        texts: {
            placemarkBeginText: '<strong>Начало маршрута</strong>',
            placemarkEndText: '<strong>Конец маршрута</strong>',
            units: {km: 'км.', m: 'м.'},
            hintDistance: 'растояния - {value}',
            hintElavation: 'высота - {value} м.',
            hintDate: '{value}',
            placemarkBeginCaption: 'Начало',
            placemarkEndCaption: 'Кoнец',

            highcharts: {
                resetZoom: 'Сбросить масштаб',
                resetZoomTitle: 'Сбросить уровень масштабирования'
            }
        }
    },

    elements: {},
    map: null,
    mapCursorAccessor: null,

    initialize: function (options) {
        this.parent(options);
        this.grabObjects(this.elements, this.options.objects);

        this.bootstrap();
    },

    bootstrap: function () {
        this.data = this.options.data;
        this.points = Array.from(this.data.points);

        if (window.ymaps && typeOf(this.elements.map) === 'element') {
            ymaps.ready(this.load.bind(this));
        }

        if (typeOf(this.elements.chart) === 'element') {
            Highcharts.setOptions({
                lang: this.iGet('text::highcharts')
            });

            this.drawChart();
        }
    },

    load: function () {
        this.map = new ymaps.Map(this.elements.map, Object.merge({
            type: this.options.settings.mapType,
            controls: ['typeSelector', 'zoomControl', 'rulerControl']
        }, this.options.map), {
            suppressMapOpenBlock: true
        });

        this.createPolyline();

        if (!!this.options.settings.mapHint) {
            this.showPointsDetails();
        }

        if (!!this.options.settings.mapPlacemarks) {
            this.setPlacemarks();
        }

        this.map.setBounds(this.map.geoObjects.getBounds(), {
            checkZoomRange: true
        });
    },

    setPlacemarks: function () {
        if (this.points.length > 0) {
            Object.each({
                begin: this.points[0],
                end: this.points.getLast()
            }, function (point, name) {
                var hintText = this.iGet('text::placemark' + name.capitalize() + 'Text');

                if (!!point.time && Date.isValid(Date.parse(point.time))) {
                    hintText += '<br/>' + Date.parse(point.time).format('%d %B %Y г. %H:%M');
                }

                this.createPlacemarks([point.lat, point.lon], hintText, name);
            }, this);
        }
    },

    createPolyline: function () {
        var points = [];
        this.points.each(function (point) {
            points.push([point.lat, point.lon]);
        });

        var polyline = new ymaps.GeoObject({
            geometry: {
                type: 'LineString',
                coordinates: points
            }
        }, {
            draggable: false,
            cursor: 'pointer',
            geodesic: true,
            strokeColor: this.options.settings.mapRouteColor,
            strokeWidth: 2,
        });

        this.map.geoObjects.add(polyline);
    },

    createPlacemarks: function (coordinates, hintText, name) {
        this.map.geoObjects.add(new ymaps.Placemark(coordinates, {
            hintContent: hintText,
            iconCaption: this.iGet('text::placemark' + name.capitalize() + 'Caption')
        }, {
            preset: 'islands#' + this.options.settings.mapPlacemarkColor,
        }));
    },

    getPointShortData: function (point) {
        var pool = [];

        Array.from(this.options.settings.mapHintFields).each(function (field) {
            if (!!point[field]) {
                switch (field) {
                    case 'time':
                        var dateTime = Date.parse(point[field]);

                        if (Date.isValid(dateTime)) {
                            pool.push(this.iGet('text::hintDate', {
                                value: dateTime.format('%d %B %y г. %H:%M')
                            }));
                        }
                        break;

                    case 'distance':
                        var valueDistance = parseFloat(point[field]);

                        if (!isNaN(valueDistance)) {
                            pool.push(this.iGet('text::hintDistance', {
                                value: this.iGet('helper::getDistance', valueDistance, this.iGet('text::units'))
                            }));
                        }
                        break;

                    case 'ele':
                        var valueElavation = parseFloat(point[field]);

                        if (!isNaN(valueElavation)) {
                            pool.push(this.iGet('text::hintElavation', {
                                value: Number.round(valueElavation, 0)
                            }));
                        }
                        break;
                }
            }
        }, this);

        return pool;
    },

    showPointsDetails: function () {
        this.hintButton = new ymaps.control.Button({
            data: {
                content: '',
            },
            state: {
                enabled: false
            },
            options: {
                layout: ymaps.templateLayoutFactory.createClass(
                    "<div class='map-route-hint-button {% if state.enabled %}map-route-hint-button-active{% endif %}'>" +
                    "{{ data.content }}" +
                    "</div>"
                ),
                maxWidth: 150
            }
        });
        this.map.controls.add(this.hintButton, {
            float: 'left',
            floatIndex: 0
        });

        this.map.events.add('mousemove', function (e) {
            var point = this.points.reduce(function (prev, curr) {
                var prevDistance = ymaps.coordSystem.geo.getDistance(e.get('coords'), [prev.lat, prev.lon]),
                    currDistance = ymaps.coordSystem.geo.getDistance(e.get('coords'), [curr.lat, curr.lon]);

                return (prevDistance < currDistance) ? prev : curr;
            });

            this.hintButton.state.set('enabled', false);

            if (this.mapCursorAccessor && this.mapCursorAccessor.getKey() === 'pointer') {
                this.mapCursorAccessor.remove();
                this.mapCursorAccessor = null;
            }

            if (!!point) {
                var dist = ymaps.coordSystem.geo.getDistance(e.get('coords'), [point.lat, point.lon]);

                if (dist < this.options.map.minRouteSnapDistance) {
                    if (this.mapCursorAccessor === null) {
                        this.mapCursorAccessor = this.map.cursors.push('pointer');
                    }

                    var shortData = this.getPointShortData(point);
                    if (shortData.length > 0) {
                        this.hintButton.data.set('content', shortData.join(', '));
                        this.hintButton.state.set('enabled', true);
                    }
                }
            }
        }, this);
    },

    drawChart: function () {
        if (this.points.length === 0) {
            return;
        }

        var data = [];
        this.points.each(function (point) {
            data.push([Number.round(point.distance / 1000, 1), !isNaN(parseFloat(point.ele)) ? Number.round(point.ele, 0) : 0]);
        });

        data.sort(function (a, b) {
            return a[0] - b[0];
        });

        Highcharts.chart(this.elements.chart, {
            chart: {
                type: 'area',
                zoomType: !!this.options.settings.chartZoom ? 'x' : void 0,
                panning: true,
                panKey: 'shift',
            },

            exporting: {
                enabled: false
            },

            title: {
                text: !!this.options.settings.chartName ? String(this.options.settings.chartName).replace(/(?:\r\n|\r|\n)/g, '<br />') : null
            },

            xAxis: {
                labels: {
                    format: '{value} км.'
                },
                minRange: 1,
                title: {
                    text: 'Расстояние'
                }
            },

            yAxis: {
                startOnTick: false,
                endOnTick: false,
                title: {
                    text: null
                },
                labels: {
                    format: '{value} m'
                }
            },

            tooltip: {
                headerFormat: 'Растояние: {point.x} км.<br>',
                pointFormat: 'Высота: {point.y} м.',
                shared: true
            },

            legend: {
                enabled: false
            },

            series: [{
                data: data,
                lineColor: this.options.settings.chartLineColor,
                color: this.options.settings.chartBaseColor,
                fillOpacity: 0.5,
                name: 'Elevation',
                marker: {
                    enabled: false
                },
                threshold: 0
            }]
        });
    }
});