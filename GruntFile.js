//Gruntfile
'use strict';

module.exports = function(grunt) {
    require('load-grunt-tasks')(grunt);
    //Initializing the configuration object
    grunt.initConfig({
        jshint: {
            all: ['./app/assets/javascript/**/*.js'],
            options: {
                force: true,
                shadow: true
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
                    './assets/css/photobum.min.css': [
                        './build/css/compiled.sass.css'
                    ],
                    './assets/css/photobumadmin.min.css': [
                        "./build/css/admin.sass.css",
                        './bower_components/bootstrap/dist/css/bootstrap.css',
                        './bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css',
                    ]
                }
            }
        },
        //JS
        uglify: {
            dev: {
                files: {
                    './assets/js/photobum.js': [
                        './build/js/_bower.js',
                        './app/assets/javascript/3rdParty/*.js',
                        './app/assets/javascript/Photobum/**/*.js'
                    ],
                    './assets/js/photobumadmin.js': [
                        './app/assets/javascript/PhotobumAdmin/**/*.js',
                        './bower_components/tinymce/themes/inlite/theme.js',
                        './bower_components/tinymce/plugins/table/plugin.js',
                        './bower_components/tinymce/plugins/image/plugin.js',
                        './bower_components/tinymce/plugins/link/plugin.js',
                        './bower_components/tinymce/plugins/paste/plugin.js',
                        './bower_components/tinymce/plugins/contextmenu/plugin.js',
                        './bower_components/tinymce/plugins/textpattern/plugin.js',
                        './bower_components/tinymce/plugins/autolink/plugin.js',
                        './bower_components/bootstrap-switch/dist/js/bootstrap-switch.js'
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
                    './assets/js/photobum.min.js': [
                        './assets/js/photobum.js'
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
                src: ['./app/assets/javascript/first.js', './build/js/_bower.js','./build/js/photobum.js' ],
                dest: './build/js/photobum.min.js'
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
        'ftp-deploy': {
            build: {
            auth: {
              host: '194.135.87.101',
              port: 21,
              authKey: 'key1'
            },
            src: '/Users/mindevieras/sites/album/',
            dest: '/public_html/album',
            exclusions: [
                '/Users/mindevieras/sites/album/**/.DS_Store',
                '/Users/mindevieras/sites/album/**/Thumbs.db',
                '/Users/mindevieras/sites/album/.git',
                '/Users/mindevieras/sites/album/.sass-cache',

                '/Users/mindevieras/sites/album/app/assets',

                '/Users/mindevieras/sites/album/app/assets/images/**',
                '/Users/mindevieras/sites/album/app/assets/fonts/**',
                '/Users/mindevieras/sites/album/app/assets/deps/**',
                '/Users/mindevieras/sites/album/assets/js/photobum.js.map',
                '/Users/mindevieras/sites/album/assets/js/photobumadmin.js.map',

                '/Users/mindevieras/sites/album/bower_components',
                '/Users/mindevieras/sites/album/node_modules',

                '/Users/mindevieras/sites/album/build',
                '/Users/mindevieras/sites/album/uploads/**',
                '/Users/mindevieras/sites/album/media/albums/**',
                '/Users/mindevieras/sites/album/media/styles/**',
                '/Users/mindevieras/sites/album/vendor',

                '/Users/mindevieras/sites/album/.ftppass',
                '/Users/mindevieras/sites/album/.gitignore',
                '/Users/mindevieras/sites/album/npm-debug.log',
                '/Users/mindevieras/sites/album/bower.json',
                '/Users/mindevieras/sites/album/composer.json',
                '/Users/mindevieras/sites/album/composer.lock',
                '/Users/mindevieras/sites/album/GruntFile.js',
                '/Users/mindevieras/sites/album/LICENSE',
                '/Users/mindevieras/sites/album/LocalConfig.php',
                '/Users/mindevieras/sites/album/LocalConfig.sample.php',
                '/Users/mindevieras/sites/album/package.json',
                '/Users/mindevieras/sites/album/README.md',
                ]
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

    grunt.registerTask('hint', [
        'jshint'
    ]);

    grunt.registerTask('deploy', [
        'bower_concat',
        'uglify:dev',
        'uglify:prod',
        'concat',
        'sass',
        'cssmin',
        'ftp-deploy'
    ]);

    grunt.registerTask('metrics', [
        'phpunit',
        'shell:metrics'
    ]);

    grunt.registerTask('html', 'Lint a supplied URL', function(url,PHPSESSIONID){
        grunt.task.run(['shell:lint_html:'+url+':'+PHPSESSIONID]);
    })
};
