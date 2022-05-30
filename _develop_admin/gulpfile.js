const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const pug = require('gulp-pug');
const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const browserSync = require('browser-sync');
const postcss = require('gulp-postcss');
const cssImport = require('postcss-import');

// ソースファイル
const srcScssFiles = './src/scss/*.scss';
const srcPugBaseDir = './src/pug';
const srcPugFiles = srcPugBaseDir + "/**/*.pug";
const srcPugExcludeFiles = "!./src/pug/**/_*.pug";
const srcJsxFiles = ['./src/scripts/**/*.*'];
const srcImgFiles = './src/images/**/*.*';

// 出力先
const destPublicBase = '../public_html/';
const destBase = destPublicBase + 'admin';
const destIndexHtml = 'index.html';
const destHtmlFiles = destBase + '/*.html';
const destCssDir = destBase + '/css';
const destCssFiles = destBase + '/css/*.css';
const destHtmlDir = destBase;
const destJsDir = destBase + '/js';
const destJsFiles = destBase + '/js/*.js';
const destImgDir = destBase + '/images';

const browserOpenPath = 'admin';


const webpackConfig = require('./webpack.config');

const compileSass = (done) => {
  const plugins = [
    cssImport({
      path: ['node_moudles' ]
    })
  ];
  gulp
    .src(srcScssFiles, { sourcemaps: true })
    .pipe(
      sass({
        outputStyle: "compressed", // expanded, compressed
      })
      .on("error", sass.logError)
    )
    .pipe(postcss(plugins))
    .pipe(
      autoprefixer({
        cascade: false,
        grid: true
      })
    )
    .pipe(gulp.dest(destCssDir, { sourcemaps: false }));

  done();
};

const compilePug = (done) => {
  gulp
    .src([srcPugFiles, srcPugExcludeFiles])
    .pipe(
      pug({
        pretty: true,
        basedir: srcPugBaseDir,
      })
    )
    .pipe(gulp.dest(destHtmlDir));

  done();
};

const copyImage = (done) => {
  gulp
    .src(srcImgFiles)
    .pipe(gulp.dest(destImgDir));
  done();
};

const bundleWebpack = (done) => {
  webpackStream(webpackConfig, webpack)
    .pipe(gulp.dest(destJsDir));
  
  done();
};

const watchFiles = (done) => {
  gulp.watch([srcPugFiles, srcPugExcludeFiles], compilePug);
  gulp.watch(srcScssFiles, compileSass);
  gulp.watch(srcJsxFiles, bundleWebpack);
  gulp.watch(srcImgFiles, copyImage);

  gulp.watch(destHtmlFiles, reloadBrowser);
  gulp.watch(destCssFiles, reloadBrowser);
  gulp.watch(destJsFiles, reloadBrowser);
  done();
};

const reloadFile = (done) => {
  browserSync.init({
    server:  {
      baseDir: destPublicBase,
      index: destIndexHtml,
    },
    startPath: browserOpenPath
  });
  done();
};

const reloadBrowser = (done) => {
  browserSync.reload();
  done();
};

exports.default = gulp.series(
  watchFiles, reloadFile, compileSass, compilePug, bundleWebpack, copyImage
)
