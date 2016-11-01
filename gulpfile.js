var gulp = require('gulp');
var gutil = require('gulp-util');
var less = require('gulp-less');

gulp.task('default', ['less'], function() {
	return gutil.log('Gulp is running!')
});


gulp.task('less', function () {
	return gulp.src('./public/css/less/*.less')
		//.pipe(less({
		//	paths: [ path.join(__dirname, 'less', 'includes') ]
		//}))
		.pipe(less())
		.pipe(gulp.dest('./public/css'));
});

gulp.task('lessexemple', function(){
  gulp.src('./normal.less')
  .pipe(less())
  .pipe(gulp.dest('build'));
});