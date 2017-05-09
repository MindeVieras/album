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
        'ftp-deploy': {
            build: {
            auth: {
              host: '194.135.87.101',
              port: 21,
              authKey: 'key1'
            },
            src: '/Users/minde/Sites/album/',
            dest: '/public_html/album',
            exclusions: [
                '/Users/minde/Sites/album/bower_components',
                '/Users/minde/Sites/album/node_modules',
                '/Users/minde/Sites/album/build',
                '/Users/minde/Sites/album/vendor',

                '/Users/minde/Sites/album/media',
                '/Users/minde/Sites/album/cache',
                '/Users/minde/Sites/album/uploads',
                '/Users/minde/Sites/album/app/assets',
                '/Users/minde/Sites/album/assets/fonts',
                '/Users/minde/Sites/album/assets/tinymce',
                '/Users/minde/Sites/album/assets/css/font-awesome.min.css',
                '/Users/minde/Sites/album/assets/js/bower.js',

                //'/Users/minde/Sites/album/install',
                '/Users/minde/Sites/album/install/assets/css/sass',
                '/Users/minde/Sites/album/install/assets/js/dev',

                '/Users/minde/Sites/album/**/*.map',
                '/Users/minde/Sites/album/**/.DS_Store',
                '/Users/minde/Sites/album/**/Thumbs.db',
                '/Users/minde/Sites/album/.git',
                '/Users/minde/Sites/album/.sass-cache',
                '/Users/minde/Sites/album/.ftppass',
                '/Users/minde/Sites/album/.gitignore',
                '/Users/minde/Sites/album/npm-debug.log',
                '/Users/minde/Sites/album/bower.json',
                '/Users/minde/Sites/album/composer.lock',
                '/Users/minde/Sites/album/GruntFile.js',
                '/Users/minde/Sites/album/LICENSE',
                '/Users/minde/Sites/album/LocalConfig.php',
                '/Users/minde/Sites/album/LocalConfig.sample.php',
                '/Users/minde/Sites/album/package.json',
                '/Users/minde/Sites/album/README.md',
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
                files: ['./app/assets/sass/**/*.scss'],
                tasks: [
                    'sass:admin'
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
    grunt.loadNpmTasks('grunt-ftp-deploy');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Task definition
    grunt.registerTask('default', [
        'watch'
    ]);

    grunt.registerTask('deploy', [
        'sass:install',
        'sass:deploy',
        'uglify:install',
        'uglify:admin',
        'ftp-deploy'
    ]);

    grunt.registerTask('admin', [
        'sass:admin',
        'bower_concat:admin',
        'uglify:admin',
        'watch'
    ]);

    grunt.registerTask('installer', [
        'sass:install',
        'uglify:install',
        'watch:install'
    ]);

};
