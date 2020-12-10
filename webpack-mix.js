const mix = require('laravel-mix');

mix.setPublicPath('dist');

mix.js('assets/admin/js/admin-app.js', 'admin/js');
mix.sass('assets/admin/sass/admin-app.scss', 'admin/css');
