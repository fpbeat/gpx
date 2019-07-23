var gulp = require('gulp'),
    path = require('path'),
    util = require('gulp-util'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    less = require('gulp-less'),
    plumber = require('gulp-plumber'),
    size = require('gulp-size'),
    notify = require('gulp-notify'),
    order = require('gulp-order'),
    autoprefixer = require('gulp-autoprefixer'),
    minifyCss = require('gulp-minify-css'),
    rename = require('gulp-rename'),
    gulpif = require('gulp-if');
gulpif = require('gulp-if');

var isProduction = util.env.env === 'production' || util.env.env === 'p',
    minPrefix = isProduction ? '.min' : '',
    processorType = util.env.type || 'web';

var paths = {
    css: ['assets/styles/' + processorType + '/**/*.{css,less}'],
    javascript: ['assets/js/extras/**/*.js', 'assets/js/' + processorType + '/**/*.js']
};

gulp.task('js', function () {
    return gulp.src(paths.javascript)
        .pipe(plumber({
            errorHandler: function (err) {
                notify.onError({
                    title: "Gulp",
                    subtitle: "Failure!",
                    message: "Error: <%= error.message %>"
                })(err);

                this.emit('end');
            }
        }))
        .pipe(order([
            'assets/js/web/extras/mootools-core.js',
            'assets/js/web/extras/mootools-more.js',
            'assets/js/web/extras/mootools-extra.js',
            'assets/js/web/core.class.js',
            'assets/js/web/bootstrap.class.js'
        ], {
            base: '.'
        }))
        .pipe(gulpif(isProduction, uglify()))
        .on('error', function (err) { console.log(err); })
        .pipe(concat(processorType + minPrefix + '.js', {
            newLine: ';'
        }))
        .pipe(size())
        .pipe(plumber.stop())
        .pipe(gulp.dest('assets/build'));
});

gulp.task('css', function () {
    var lessCondition = function (file) {
        return path.extname(file.path) === '.less';
    };

    return gulp.src(paths.css)
        .pipe(plumber({
            errorHandler: function (err) {
                notify.onError({
                    title: "Gulp",
                    subtitle: "Failure!",
                    message: "Error: <%= error.message %>"
                })(err);

                this.emit('end');
            }
        }))
        .pipe(order([], {
            base: '.'
        }))
        .pipe(gulpif(lessCondition, less()))
        .pipe(autoprefixer({
            browsers: ['last 10 versions'],
            cascade: false
        }))
        .pipe(gulpif(isProduction, minifyCss({
            advanced: false,
            aggressiveMerging: false
        })))
        .pipe(concat(processorType + minPrefix + '.css'))
        .pipe(size())

        .pipe(plumber.stop())
        .pipe(gulp.dest('assets/build'))
});


// re-run the task when a file changes
gulp.task('watch', function () {
    gulp.watch(paths.css, ['css']);
    gulp.watch(paths.javascript, ['js']);
});

// The default task
gulp.task('default', ['css', 'js']);