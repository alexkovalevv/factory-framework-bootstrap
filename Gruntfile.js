'use strict';

module.exports = function(grunt) {

	var banner = '/*!\n' +
		' * <%= pkg.title %> - v<%= pkg.version %>, <%= grunt.template.today("yyyy-mm-dd") %> \n' +
		' * Webcraftic factory build \n' +
		' * \n' +
		' * <%= pkg.copyright %> \n' +
		' * Site: http://webcraftic.com \n' +
		' * Support: http://webcraftic.com/contact-us/ \n' +
		'*/\n';

	//grunt.loadTasks("tasks");

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		build_bootstrap: {
			targets: {}
		}
	});

	grunt.registerMultiTask("build_bootstrap", "Build bootstrap", function() {

		grunt.config.set('uglify', {
			options: {
				preserveComments: 'some',
				banner: banner + '\n'
			},
			js: {
				files: [
					{
						expand: true,
						src: ['assets/js/*.js', '!assets/js/*.min.js'],
						dest: 'assets/js-min/',
						cwd: '.',
						rename: function(dst, src) {
							src = src.replace('assets/js', 'assets/js-min');
							src = src.replace('.js', '.min.js');
							return src;
						}
					}
				]
			}
		});

		grunt.config.set('cssmin', {
			target: {
				options: {
					preserveComments: 'some',
					banner: banner + '\n'
				},
				files: [
					{
						expand: true,
						src: ['assets/css/*.css', '!assets/css/*.min.css'],
						dest: 'assets/css-min/',
						cwd: '.',
						rename: function(dst, src) {
							src = src.replace('assets/css', 'assets/css-min');
							src = src.replace('.css', '.min.css');
							return src;
						}
					}
				]
			}
		});

		grunt.config.set('clean', {
			before: [
				'assets/js-min/*',
				'assets/css-min/*'
			]
			//after: ['temp']
		});

		//grunt.task.run(['clean:before', 'preprocess', 'uglify', 'cssmin', 'copy', 'clean:after']);

		grunt.task.run(['clean:before', 'uglify', 'cssmin']);
	});

	//grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-clean');
	//grunt.loadNpmTasks('grunt-contrib-copy');
	//grunt.loadNpmTasks('grunt-contrib-obfuscator');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	//grunt.loadNpmTasks('grunt-onpress-preprocess');

};
