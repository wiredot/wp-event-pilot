'use strict';

var gulp = require('gulp'),
	concat = require('gulp-concat'),
	maps = require('gulp-sourcemaps'),
	sass = require('gulp-sass'),
	plumber = require('gulp-plumber'),
	notify = require('gulp-notify');

var options = {
	dist: 'dist',
	assets: 'assets',
	skins: 'skins',
	dist: 'dist',
	src: 'src'
}

gulp.task('js', function() {
	return gulp.src( options.src + '/js/*.js')
		.pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
		.pipe(maps.init())
		// .pipe(concat('wpep.js'))
		.pipe(maps.write('./'))
		.pipe(gulp.dest( options.assets + '/js'))
		.pipe(notify({
			message: 'all done',
			title: 'JS'
		}));
});

gulp.task('scss', function() {
	return gulp.src( options.src + '/scss/*.scss')
		.pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
		.pipe(maps.init())
		.pipe(sass(''))
		.pipe(maps.write('./'))
		.pipe(gulp.dest( options.assets + '/css'))
		.pipe(notify({
			message: 'all done',
			title: 'SCSS'
		}));
});

gulp.task('default', ['scss', 'js']);

gulp.task('watch', function() {
	gulp.watch( options.src + '/js/*.js', ['js']);
	gulp.watch( options.src + '/scss/**/*.scss', ['scss']);
});
