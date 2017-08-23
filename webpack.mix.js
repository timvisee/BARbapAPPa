let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

/**
 * Copy static assets.
 */
mix.copyDirectory(
    'resources/assets/img',
    'public/img'
);

/**
 * Compile the application assets.
 */
mix.js('resources/assets/js/app.js', 'public/js');
mix.sass('resources/assets/sass/app.scss', 'public/css');

/**
 * Package jQuery and jQuery mobile resources.
 */
mix.scripts([ 'resources/assets/vendor/jquery/jquery-2.1.4.js',
    'resources/assets/js/jquery.mobile.settings.js',
    'resources/assets/vendor/jquery-mobile/jquery.mobile-1.4.5.js'
], 'public/js/jquery-packed.js');
mix.styles([
    'resources/assets/vendor/jquery-mobile/jquery.mobile-1.4.5.css'
], 'public/css/jquery-mobile.css');
mix.copyDirectory(
    'resources/assets/vendor/jquery-mobile/images',
    'public/css/images'
);

/**
 * Package Glyphicons resources.
 */
mix.copyDirectory(
    'resources/assets/vendor/glyphicons/fonts',
    'public/fonts'
);
mix.copyDirectory(
    'resources/assets/vendor/glyphicons-halflings/fonts',
    'public/fonts'
);
mix.styles([
    'resources/assets/vendor/glyphicons/css/glyphicons.css',
    'resources/assets/vendor/glyphicons-halflings/css/glyphicons-halflings.css'
], 'public/css/glyphicons-packed.css');
