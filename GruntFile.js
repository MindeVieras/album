//Gruntfile
'use strict';

module.exports = function(grunt) {

    //Initializing the configuration object
    grunt.initConfig({
        sass: {
            admin: {
                options: {
                    style: 'expanded'
                },
                files: {
                    './assets/css/admin.min.css': './app/assets/sass/admin/main.scss'
                }
            }
        },
        bower_concat: {
            admin: {
                dest: {
                    js: './app/assets/js/Admin/plugins/bower.js',
                    scss: './app/assets/sass/admin/plugins/_bower.scss'
                }
            }
        },
        //JS
        uglify: {
            admin: {
                files: {
                    './assets/js/admin.min.js': [
                        './app/assets/js/Admin/plugins/*.js',
                        './app/assets/js/Admin/*.js'
                    ]
                },
                options: {
                    beauty: true,
                    mangle: false,
                    compress: false,
                    sourceMap: true
                }
            }
        },
        watch: {
            js: {
                files: ['./app/assets/js/Admin/**/*.js'],
                tasks : [
                    'uglify:admin'
                ]
            },
            sass: {
                files: ['./app/assets/sass/admin/**/*.scss'],
                tasks: [
                    'sass:admin'
                ]
            },
            templates: {
                files: ['./app/templates/**/*.html']
            },
            options: {
                livereload: true
            }
        }
    });


    grunt.loadNpmTasks('grunt-bower-concat');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Task definition
    grunt.registerTask('default', [
        'watch'
    ]);

    grunt.registerTask('admin', [
        'sass:admin',
        'bower_concat:admin',
        'uglify:admin',
        'watch'
    ]);

};
