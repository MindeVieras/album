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
            deploy: {
                options: {
                    style: 'compressed'
                },
                files: {
                    './assets/css/admin.min.css': './app/assets/sass/admin/main.scss'
                }
            }
        },
        bower_concat: {
            admin: {
                dest: {
                    js: './assets/js/bower.js'
                }
            }
        },
        //JS
        uglify: {
            admin: {
                files: {
                    './assets/js/admin.min.js': [
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
                '/Users/mindevieras/sites/album/bower_components',
                '/Users/mindevieras/sites/album/node_modules',
                '/Users/mindevieras/sites/album/build',
                '/Users/mindevieras/sites/album/vendor',

                '/Users/mindevieras/sites/album/media',
                '/Users/mindevieras/sites/album/uploads',
                '/Users/mindevieras/sites/album/app/assets',
                '/Users/mindevieras/sites/album/assets/fonts',
                '/Users/mindevieras/sites/album/assets/css/font-awesome.min.css',
                '/Users/mindevieras/sites/album/assets/js/bower.js',

                '/Users/mindevieras/sites/album/**/*.map',
                '/Users/mindevieras/sites/album/**/.DS_Store',
                '/Users/mindevieras/sites/album/**/Thumbs.db',
                '/Users/mindevieras/sites/album/.git',
                '/Users/mindevieras/sites/album/.sass-cache',
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
    grunt.loadNpmTasks('grunt-ftp-deploy');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Task definition
    grunt.registerTask('default', [
        'watch'
    ]);

    grunt.registerTask('deploy', [
        'sass:deploy',
        'uglify:admin',
        'ftp-deploy'
    ]);

    grunt.registerTask('admin', [
        'sass:admin',
        'bower_concat:admin',
        'uglify:admin',
        'watch'
    ]);

};
