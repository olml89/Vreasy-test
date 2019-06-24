
//require dependencies
var gulp  		= require('gulp');
var pump 		= require('pump');
var concat 		= require('gulp-concat');
var uglify 		= require('gulp-uglify-es').default;	//https://stackoverflow.com/questions/40521506/gulpuglifyerrorunable-to-minify-javascript
var sourcemaps 	= require('gulp-sourcemaps');
var sass 		= require('gulp-sass'); 


//Compile all application JS files
gulp.task('pack-application-js', function(callback) {

	pump([
		gulp.src([
				'assets/js/!(application.js)',
				'assets/js/application.js'			//make sure application.js is appended last
			]),
			concat('bundle.min.js'),
			sourcemaps.write('/maps'),
			//uglify(),
			gulp.dest('www/js')
		], 
		
		callback
	);

});


//Compile and minify all application SCSS files
gulp.task('pack-application-scss', function(callback) {

	pump([
		gulp.src([
				'assets/scss/styles.scss',
			]),
			sourcemaps.init(),
			sass({
				outputStyle: 'compressed'
			}),
			concat('styles.min.css'),
			sourcemaps.write('/maps'),
			gulp.dest('www/css')
		], 

		callback
	);

});


//Watch automatically for file changes in the application files
//https://stackoverflow.com/questions/39665773/gulp-error-watch-task-has-to-be-a-function
gulp.task('watch', function() {
	gulp.watch('assets/js/*.js', gulp.series('pack-application-js'));
	gulp.watch('assets/js/**/*.js', gulp.series('pack-application-js'));
	gulp.watch('assets/scss/*.scss', gulp.series('pack-application-scss'));
	gulp.watch('assets/scss/**/*.scss', gulp.series('pack-application-scss'));
});


//Default task: compile the appication and watch for changes
//gulp.task('default', ['pack-application-js', 'pack-application-scss', 'watch']); //https://codeburst.io/switching-to-gulp-4-0-271ae63530c0
gulp.task('default', gulp.series(gulp.parallel('pack-application-js', 'pack-application-scss', 'watch')));


