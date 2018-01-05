var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    .enableVersioning(Encore.isProduction())

    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/media', './assets/js/media.js')
    .addEntry('js/album', './assets/js/album.js')
    .addEntry('js/dropzone', './assets/js/dropzone.js')
    .addEntry('js/lightgallery', './assets/js/lightgallery.js')
    .addStyleEntry('css/app', './assets/css/app.scss')
    .addStyleEntry('css/lightgallery', './assets/css/lightgallery.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
