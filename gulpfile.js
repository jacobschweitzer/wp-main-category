'use strict';
const gulp = require('gulp');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');
const rename = require('gulp-rename');

// Gulp task to minify JavaScript files
gulp.task( 'scripts', function() {
	return gulp.src( ['js/src/*.js'] )
		.pipe( babel({
			presets: ['@babel/env']
		} ) )
		.pipe( uglify() )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'js/dist' ) );
});
