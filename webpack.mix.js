const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js').vue();
mix.js('resources/js/student_app.js', 'public/js/student').vue()
mix.sass('resources/css/app.scss', 'public/css');
