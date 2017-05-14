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
            },
            web: {
                options: {
                    style: 'expanded'
                },
                files: {
                    './assets/css/web.min.css': './app/assets/sass/web/main.scss'
                }
            },
            install: {
                options: {
                    style: 'compressed'
                },
                files: {
                    './install/assets/css/install.min.css': './install/assets/css/sass/main.scss'
                }
            }
        },
        bower_concat: {
            admin: {
                dest: {
                    js: './assets/js/bower.js'
                },
                exclude: [
                'exif-js'
                ]
            }
        },
        //JS
        uglify: {
            admin: {
                files: {
                    './assets/js/admin.min.js': [
                        './bower_components/tinymce/themes/modern/theme.js',
                        './bower_components/exif-js/exif.js',
                        './app/assets/js/Admin/*.js'
                    ]
                },
                options: {
                    beauty: true,
                    mangle: false,
                    compress: false,
                    sourceMap: true
                }
            },
            web: {
                files: {
                    './assets/js/web.min.js': [
                        './app/assets/js/Web/*.js'
                    ]
                },
                options: {
                    beauty: true,
                    mangle: false,
                    compress: false,
                    sourceMap: true
                }
            },
            install: {
                files: {
                    './install/assets/js/install.min.js': [
                        './install/assets/js/dev/*.js'
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
            js_admin: {
                files: ['./app/assets/js/Admin/**/*.js'],
                tasks : [
                    'uglify:admin'
                ]
            },
            js_web: {
                files: ['./app/assets/js/Web/**/*.js'],
                tasks : [
                    'uglify:web'
                ]
            },
            sass_admin: {
                files: ['./app/assets/sass/admin/**/*.scss'],
                tasks: [
                    'sass:admin'
                ]
            },
            sass_web: {
                files: ['./app/assets/sass/web/**/*.scss'],
                tasks: [
                    'sass:web'
                ]
            },
            templates: {
                files: ['./app/templates/**/*.html']
            },
            classes: {
                files: ['./app/classes/**/*.php']
            },
            install: {
                files: ['./install/assets/css/sass/**/*.scss', './install/assets/js/dev/**/*.js'],
                tasks: [
                    'sass:install',
                    'uglify:install'
                ]
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

    grunt.registerTask('web', [
        'sass:web',
        'uglify:web',
        'watch'
    ]);

    grunt.registerTask('installer', [
        'sass:install',
        'uglify:install',
        'watch:install'
    ]);

};
