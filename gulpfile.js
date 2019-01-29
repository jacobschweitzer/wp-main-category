'use strict';
const gulp = require('gulp');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');

// Gulp task to minify JavaScript files
gulp.task( 'scripts', function() {
	return gulp.src( 'js/src/*.js' )
		.pipe( babel({
			presets: ['@babel/env']
		} ) )
		.pipe( uglify() )
		.pipe( gulp.dest( 'js/dist' ) );
});
