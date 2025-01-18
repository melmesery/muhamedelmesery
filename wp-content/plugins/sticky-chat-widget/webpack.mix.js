const mix = require('laravel-mix');

mix.sass('src/admin/css/style.scss', 'dist/admin/css');
mix.sass('src/admin/css/custom.scss', 'dist/admin/css');
mix.sass('src/admin/css/style-rtl.scss', 'dist/admin/css');
mix.sass('src/admin/css/sign-up.scss', 'dist/admin/css')
    .options({
        processCssUrls: false
    });
mix.sass('src/admin/css/widget-analytics.scss', 'dist/admin/css');
mix.sass('src/admin/css/leads-css.scss', 'dist/admin/css');
mix.sass('src/admin/css/deactivate-plugin.scss', 'dist/admin/css');
mix.sass('src/admin/css/admin-style.scss', 'dist/admin/css');
mix.sass('src/admin/css/integration.scss', 'dist/admin/css');

mix.js('src/admin/js/deactivate-plugin.js', 'dist/admin/js');
mix.js('src/admin/js/common-script.js', 'dist/admin/js');
mix.js('src/admin/js/script.js', 'dist/admin/js');
mix.js('src/admin/js/leads-js.js', 'dist/admin/js');

mix.sass('src/front/css/front.scss', 'dist/front/css');

mix.js('src/front/js/script.js', 'dist/front/js');