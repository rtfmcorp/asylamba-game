var gulp = require('gulp');
var gutil = require('gulp-util');
var less = require('gulp-less');
var rename = require('gulp-rename');
var replace = require('gulp-replace');

gulp.task('default', ['less'], function() {
	return gutil.log('Gulp is running!')
});

gulp.task('less', function(){
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