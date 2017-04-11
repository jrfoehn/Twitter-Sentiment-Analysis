const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    var bootstrapPath = 'node_modules/bootstrap-sass/assets';
    mix.sass('app.scss')
        .copy(bootstrapPath + '/fonts', 'public/fonts')
        .copy(bootstrapPath + '/javascripts/bootstrap.min.js', 'public/js');

    mix.styles(['bootstrap-slider.min.css'], 'public/css/bootstrap-slider.min.css');
    mix.styles(['bootstrap-switch.min.css'], 'public/css/bootstrap-switch.min.css');
    mix.styles(['bootstrap-datetimepicker.min.css'], 'public/css/bootstrap-datetimepicker.min.css');
    mix.styles(['bootstrap-formhelpers.min.css'], 'public/css/bootstrap-formhelpers.min.css');
    mix.styles(['style.css'], 'public/css/style.css');
    mix.styles(['popover.css'], 'public/css/popover.css');
    mix.styles(['jquery-ui.min.css'], 'public/css/jquery-ui.min.css');

    mix.scripts('jquery-3.1.1.min.js');
    mix.scripts('jquery-ui.min.js');
    mix.scripts('bootstrap-slider.min.js');
    mix.scripts('bootstrap-switch.min.js');
    mix.scripts('bootstrap-datetimepicker.min.js');
    mix.scripts('bootstrap-formhelpers.min.js');
    mix.scripts('bootstrap-formhelpers-languages.js');
    mix.scripts('script.js');
    mix.copy('resources/assets/js/cytoscape.js-2.4.9', 'public/js/cytoscape.js-2.4.9');
});
