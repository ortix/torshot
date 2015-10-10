var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix.scripts([
        '../vendor/jquery/dist/jquery.js',
        '../vendor/bootstrap/dist/js/bootstrap.js',
        '../vendor/angular/angular.js',
        '../vendor/vue/dist/vue.js',
        '../vendor/vue-resource/dist/vue-resource.js'
    ], 'public/assets/js/scripts.js');
});
