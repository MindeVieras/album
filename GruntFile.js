//Gruntfile
'use strict';

module.exports = function(grunt) {
    require('load-grunt-tasks')(grunt);
    //Initializing the configuration object
    grunt.initConfig({
        jshint: {
            all: ['Gruntfile.js', './app/assets/javascript/**/*.js'],
            options: {
                force: true
            }
        },
        sass: {
            dist: {
                files: {
                    "./build/css/compiled.sass.css" : "./app/assets/stylesheets/sass/main.scss",
                    "./build/css/admin.sass.css" : "./app/assets/stylesheets/sass/admin.scss"
                }
            }
        },
        cssmin: {
            combine: {
                files: {
                    './assets/css/albumas.min.css': [
                        './build/css/compiled.sass.css'
                    ],
                    './assets/css/albumasadmin.min.css': [
                        "./build/css/admin.sass.css",
                        './bower_components/bootstrap/dist/css/bootstrap.css',
                    ]
                }
            }
        },
        //JS
        uglify: {
            dev: {
                files: {
                    './assets/js/albumas.js': [
                        './build/js/_bower.js',
                        './app/assets/javascript/3rdParty/*.js',
                        './app/assets/javascript/Albumas/**/*.js'
                    ],
                    './assets/js/albumasadmin.js': [
                        './app/assets/javascript/AlbumasAdmin/**/*.js',
                        './bower_components/tinymce/themes/inlite/theme.js',
                        './bower_components/tinymce/plugins/table/plugin.js',
                        './bower_components/tinymce/plugins/image/plugin.js',
                        './bower_components/tinymce/plugins/link/plugin.js',
                        './bower_components/tinymce/plugins/paste/plugin.js',
                        './bower_components/tinymce/plugins/contextmenu/plugin.js',
                        './bower_components/tinymce/plugins/textpattern/plugin.js',
                        './bower_components/tinymce/plugins/autolink/plugin.js'
                    ]
                },
                options: {
                    beauty: true,
                    mangle: false,
                    compress: false,
                    sourceMap: true
                }
            },
            prod: {
                files: {
                    './assets/js/albumas.min.js': [
                        './assets/js/albumas.js'
                    ]
                },
                options: {
                    mangle: true,
                    // compress: {
                    //     drop_console: true
                    // },
                    compress: true,
                    sourceMap: false
                }
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            dist: {
                src: ['./app/assets/javascript/first.js', './build/js/_bower.js','./build/js/albumas.js' ],
                dest: './build/js/albumas.min.js'
            }
        },
        bower_concat: {
            all: {
                dest: './build/js/_bower.js',
                cssDest: './build/css/_bower.css',
                mainFiles: {
                   // 'blueimp-canvas-to-blob': 'js/canvas-to-blob.js'
                    'eonasdan-bootstrap-datetimepicker': ['build/js/bootstrap-datetimepicker.min.js', 'build/css/bootstrap-datetimepicker-standalone.min.css']

                },
                bowerOptions: {
                    relative: false
                }
            }
        },
        //Automation
        notify_hooks: {
            options: {
                enabled: true,
                max_jshint_notifications: 5, // maximum number of notifications from jshint output
                success: true, // whether successful grunt executions should be notified automatically
                duration: 0.5 // the duration of notification in seconds, for `notify-send only (PICOLLECTA:: we aren't using this)
            }
        },
        concurrent: {
            JS: ['uglify','concat'],
            CSS: ['sass', 'cssmin']
        },
        phpunit: {
            classes: {
                dir: 'tests/unit/app'
            },
            options: {
                bin: 'vendor/bin/phpunit',
                configuration: 'phpunit.xml'
            }
        },
        watch: {
            js: {
                files: ['./app/assets/javascript/**/*.js', './app/templates/**/*.html'],
                tasks : [
                    'uglify:dev',
                    'concat'
                ]
            },
            sass: {
                files: ['./app/assets/stylesheets/sass/**/*.scss'],
                tasks: [
                    'sass',
                    'cssmin'
                ]
            },
            options: {
                livereload: true
            }
        }
    });

    // Task definition
    grunt.task.run('notify_hooks');

    grunt.registerTask('default', [
        'build',
        'watch'
    ]);

    grunt.registerTask('build', [
        'bower_concat',
        'uglify:dev',
        'concat',
        'sass',
        'cssmin'
    ]);

    grunt.registerTask('deploy', [
        'bower_concat',
        'uglify:dev',
        'uglify:prod',
        'concat',
        'sass',
        'cssmin'
    ]);

    grunt.registerTask('metrics', [
        'phpunit',
        'shell:metrics'
    ]);

    grunt.registerTask('html', 'Lint a supplied URL', function(url,PHPSESSIONID){
        grunt.task.run(['shell:lint_html:'+url+':'+PHPSESSIONID]);
    })
};
