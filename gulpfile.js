'use strict';

// Include Gulp & tools we'll use
var gulp = require('gulp');
var gutil = require('gulp-util');
var less = require('gulp-less');
var rename = require('gulp-rename');
var replace = require('gulp-replace');
var imagemin = require('gulp-imagemin');


// -------------------- GULP TASKS --------------------

gulp.task('default', ['less'], () => {
	return gutil.log('Gulp is running!')
});

// Build CSS with LESS sources
gulp.task('less', () => {
	// specific faction files
	for (var i = 1; i < 13; i++) {
		gulp.src(['./public/css/less/main.desktop.v3.less'])
			.pipe(replace('@color-type: 1;', '@color-type: '.concat(i, ';')))
			.pipe(less())
			.pipe(rename('main.desktop.v3.color' + i + '.css'))
			.pipe(gulp.dest('./public/css/'));
	}
	// general file
	gulp.src(['./public/css/less/main.desktop.v3.less'])
		.pipe(less())
		.pipe(gulp.dest('./public/css/'));
});

// Optimize images
gulp.task('images', () => {
	gulp.src('public/css/src/desktop-src/**/*')
		.pipe(imagemin({ progressive: true}))
		.pipe(gulp.dest('public/css/src/desktop'))
	gulp.src('public/media-src/**/*')
		.pipe(imagemin({ progressive: true }))
		.pipe(gulp.dest('public/media'))
});