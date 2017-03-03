module.exports = function(grunt) { 

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out         = 'output';

    /**
     * Remove bundled and bundling files
     */
    clean.lticlientdiagbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.lticlientdiagbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'ltiClientdiag' : root + '/ltiClientdiag/views/js' },
            modules : [{
                name: 'ltiClientdiag/controller/routes',
                include : ext.getExtensionsControllers(['ltiClientdiag']),
                exclude : ['mathJax'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.lticlientdiagbundle = {
        files: [
            { src: [out + '/ltiClientdiag/controller/routes.js'],  dest: root + '/ltiClientdiag/views/js/controllers.min.js' },
            { src: [out + '/ltiClientdiag/controller/routes.js.map'],  dest: root + '/ltiClientdiag/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('lticlientdiagbundle', ['clean:lticlientdiagbundle', 'requirejs:lticlientdiagbundle', 'copy:lticlientdiagbundle']);
};
