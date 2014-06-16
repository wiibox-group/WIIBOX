module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        less: {
            development: {
                options: {
                    paths: ["static/assets/style"]
                },
                files: {
                    "static/assets/style/style.css": "static/assets/style/style.less"
                }
            },
            production: {
                options: {
                    paths: ["static/assets/style"],
                    yuicompress: true
                },
                files: {
                    "static/assets/style/style.min.css": "static/assets/style/style.less"
                }
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.registerTask('default', ['less']);
}
