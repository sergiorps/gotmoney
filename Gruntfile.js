'use strict';

module.exports = function (grunt) {
    grunt.initConfig({
        openui5_preload: {
            component: {
                options: {
                    resources: {
                        cwd: 'public/webapp',
                        prefix: 'com/mlauffer/gotmoneyappui5',
                        src: [
                            '**/*.js',
                            '**/*.xml'
                        ]
                    },
                    dest: 'build',
                    compress: true
                },
                components: true
            }
        }
    });

    grunt.loadNpmTasks('grunt-openui5');
    //grunt.loadNpmTasks('grunt-contrib-htmlmin');
    //grunt.loadNpmTasks('grunt-contrib-cssmin');
};