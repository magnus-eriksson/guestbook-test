var gulp     = require('gulp');
var sass     = require('gulp-sass');
var cleanCSS = require('gulp-clean-css');
var rename   = require('gulp-rename');
var seq      = require('gulp-sequence');
var concat   = require('gulp-concat');
var uglify   = require('gulp-uglify');

// Paths
var dest = '../public/static';

/**
 * Sass
 */
gulp.task('sass', function () {
    gulp.src(['sass/main.scss'])
        .pipe(sass())
        .on('error', function (err) {
            console.log(err.toString());
            this.emit('end');
        })
        .pipe(gulp.dest(dest));
});

/**
 * JS
 */
gulp.task('concatJs', function () {
    gulp.src([
            './js/libs/*.js',
            './js/components/*.js',
            './js/main.js'
        ])
        .pipe(concat('main.js'))
        .pipe(gulp.dest(dest));
});

/**
 * Minify
 */
gulp.task('minifyCss', function () {
    gulp.src([dest + '/main.css'])
        .pipe(cleanCSS())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(dest));
});

gulp.task('uglifyJs', function () {
    gulp.src(dest + '/main.js')
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest(dest));
});


/**
 * Tasks
 */
gulp.task('default', ['sass', 'concatJs'], function () {
    gulp.watch(['sass/**/*.scss'], ['sass']);
    gulp.watch(['js/**/*.js'], ['concatJs']);
});

gulp.task('build', seq('sass', 'concatJs', 'minifyCss', 'uglifyJs'));
