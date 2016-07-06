'use strict';
var our_proxy_url = "aiga-show.dev:8888";

var gulp = require('gulp');
var gutil = require('gulp-util');
var del = require('del');
var uglify = require('gulp-uglify');
var gulpif = require('gulp-if');
var exec = require('gulp-exec');
var notify = require('gulp-notify');
var buffer = require('vinyl-buffer');
var argv = require('yargs').argv;
var lazypipe = require('lazypipe');

// Sass
var sass = require('gulp-sass');
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var cssNano = require('cssnano');

// BrowserSync
var browserSync = require('browser-sync');

// Javascript
var watchify = require('watchify');
var browserify = require('browserify');
var hbsfy = require('hbsfy');
var source = require('vinyl-source-stream');

// Image Optimization
var imagemin = require('gulp-imagemin');
var flatten = require('gulp-flatten');

// Linting
var jshint = require('gulp-jshint');
var stylish = require('jshint-stylish');

// Revisioning
var rev = require('gulp-rev');

// Testing Suite
var Server = require('karma').Server;


// gulp build --production
var production = !!argv.production;

// determine if we're doing a build
// and if so, bypass the livereload
var build = argv._.length ? argv._[0] === 'build' : false;
var watch = argv._.length ? argv._[0] === 'watch' : true;

// Path to the compiled assets manifest in the assets_compiled directory
var revision_manifest = './assets_compiled/revision.json';

// ----------------------------
// Error notification methods
// ----------------------------
var beep = function () {
  var os = require('os');
  var error = gulp.src('path/error.wav');
  if (os.platform() === 'linux') {
    error.pipe(exec('aplay <%= file.path %>'));
  } else {
    // mac
    error.pipe(exec('afplay <%= file.path %>'));
  }
};

var handle_error = function (task) {
  return function (err) {

    notify.onError({
      message: task + ' failed!',
      sound: false
    })(err);

    gutil.log(gutil.colors.bgRed(task + ' error:'), gutil.colors.red(err));
  };
};

function forEach(object, callback) {
    for(var prop in object) {
        if(object.hasOwnProperty(prop)) {
            callback(prop, object[prop]);
        }
    }
}

// ### Write to rev manifest
// If there are any revved files then write them to the rev manifest.
// See https://github.com/sindresorhus/gulp-rev
var write_to_revision_manifest = function (directory) {
  return lazypipe()
  .pipe(gulp.dest, './assets_compiled/' + directory)
  .pipe(rev.manifest, revision_manifest, {
    base: './assets_compiled/',
    merge: true
  })
  .pipe(gulp.dest, './assets_compiled/')();
};


// ### CUSTOM TASK METHODS
// If there are any revved files then write them to the rev manifest.
// See https://github.com/sindresorhus/gulp-rev
var tasks = {
  // ### Clean
  // `gulp clean` - Deletes the build folder entirely.
  clean: function (cb) {
    del(['./assets_compiled/'], cb);
  },

  // ### Fonts
  // `gulp fonts` - Grabs all the fonts and outputs them in a flattened directory
  // structure. See: https://github.com/armed/gulp-flatten
  fonts: function () {
    return gulp.src('./assets/fonts/**/*')
      .pipe(flatten())
      .pipe(gulp.dest('./assets_compiled/fonts'));
  },

  // ### Sass
  // `gulp sass` - Compiles, combines, and optimizes project CSS.
  // By default this task will only log a warning if a precompiler error is
  // raised.
  sass: function () {
    return gulp.src('./assets/sass/*.scss')
    // sourcemaps + sass + error handling
    .pipe(gulpif(!production, sourcemaps.init()))
    .pipe(sass({
      sourceComments: !production,
      outputStyle: production ? 'compressed' : 'nested'
    }))
    .on('error', handle_error('Sass'))
    // generate .maps
    .pipe(gulpif(!production, sourcemaps.write({
      'includeContent': false,
      'sourceRoot': '.'
    })))
    // autoprefixer
    .pipe(gulpif(!production, sourcemaps.init({
      'loadMaps': true
    })))
    .pipe(postcss([autoprefixer({browsers: ['last 2 versions']})]))
    // we don't serve the source files
    // so include scss content inside the sourcemaps
    .pipe(sourcemaps.write({
      'includeContent': true
    }))
    .pipe(gulpif(production, rev()))
    // write sourcemaps to a specific directory
    // give it a file and save
    .pipe(gulp.dest('./assets_compiled/css'))
    .pipe(write_to_revision_manifest('css/'));
  },

  // ### JS processing pipeline
  // `gulp browserify` - Runs JSHint then compiles, combines, and optimizes Bower JS
  // and project JS.
  browserify: function () {
    var js_files = {
      './app.js': './assets/js/app.js',
      './jquery.scrolldepth.js': './assets/js/jquery.scrolldepth.js',
      './typekit.tinymce.js': './assets/js/typekit.tinymce.js',
      './jquery.js': './assets/js/jquery.js'
    };

    forEach(js_files, function(js_file_name, js_file_source) {

      var bundler = browserify(js_file_source, {
        debug: production,
        cache: {}
      });

      // determine if we're doing a build
      // and if so, bypass the livereload
      var build = argv._.length ? argv._[0] === 'build' : false;

      if (watch) {
        bundler = watchify(bundler);
      }

      return bundler.transform(hbsfy, { traverse: true })
        .bundle()
        .on('error', handle_error('Browserify'))
        .pipe(source(js_file_name))
        .pipe(buffer())
        .pipe(uglify())
        .pipe(gulpif(production, rev()))
        .pipe(gulp.dest('./assets_compiled/js/'))
        .pipe(write_to_revision_manifest('js/'));
    });
  },

  // ### Images
  // `gulp images` - Run lossless compression on all the images.
  optimize: function() {
    return gulp.src('./assets/images/**/*.{gif,jpg,png,svg,ico}')
    .pipe(imagemin({
      progressive: true,
      interlaced: true,
      svgoPlugins: [{removeUnknownsAndDefaults: false}, {cleanupIDs: false}],
      // png optimization
      optimizationLevel: production ? 3 : 1
      }))
    .pipe(gulp.dest('./assets_compiled/images'));
  },

  // ### Testing suite
  // `gulp test` - Run lossless compression on all the images.
  test: function (done) {
    return new Server({
      configFile: __dirname + '/karma.conf.js',
      singleRun: true
    }, done).start();
  },
};

gulp.task('browser-sync', function () {
  browserSync({
    proxy: our_proxy_url,
    port: process.env.PORT || 3000,
    open: false,
    files: [
      "./assets_compiled/css/**/*.css",
      "./assets_compiled/js/**/*.js",
    ]
  });
});

gulp.task('reload-sass', [], function () {
  browserSync.stream( {match: "./assets_compiled/**/*.css"} )
});

gulp.task('reload-js', [], function () {
  browserSync.stream( {match: "./assets_compiled/**/*.js"} )
});

gulp.task('reload-fonts', ['fonts'], function () {
  browserSync.reload();
});

gulp.task('reload-html', function () {
  browserSync.reload();
});

// --------------------------
// CUSTOM TASKS
// --------------------------
gulp.task('clean', tasks.clean);
// for production we require the clean method on every individual task
var req = build ? ['clean'] : [];
// individual tasks
gulp.task('sass', req, tasks.sass);
gulp.task('browserify', req, tasks.browserify);
gulp.task('lint:js', tasks.lintjs);
gulp.task('fonts', req, tasks.fonts);
gulp.task('optimize', tasks.optimize);
gulp.task('test', tasks.test);

// --------------------------
// DEV/WATCH TASK
// --------------------------
gulp.task('watch', ['sass', 'browserify', 'browser-sync'], function () {

  // --------------------------
  // watch:sass
  // --------------------------
  gulp.watch('./assets/css/**/*.scss', ['sass', 'reload-sass']);

  // --------------------------
  // watch:js
  // --------------------------
  gulp.watch(['./assets/js/**/*.js', './assets/templates/**/*.hbs'], ['browserify', 'reload-js']);

  // --------------------------
  // watch:optimize
  // --------------------------
  gulp.watch('./assets/images/**/*.{gif,jpg,png,svg,ico}', ['optimize']);

  // --------------------------
  // watch:optimize
  // --------------------------
  gulp.watch('./assets/fonts/*.{*}', ['reload-fonts']);

  // --------------------------
  // watch:ruby_html
  // --------------------------
  gulp.watch('./**/*.{ru,html,haml,php,html}', ['reload-html']);


  gutil.log(gutil.colors.bgGreen('Watching for changes...'));
});

// build task
gulp.task('build', [
  'sass',
  'browserify',
  'fonts',
  'optimize'
]);

gulp.task('default', [
  'sass',
  'browserify',
  'browser-sync',
  'watch'
]);

// gulp (watch) : for development and livereload
// gulp build : for a one off development build
// gulp build --production : for a minified production build
