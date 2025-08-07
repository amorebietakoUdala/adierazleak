/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './css/app.css';

// start the Stimulus application
import './bootstrap';
import './turbo/turbo-helper';

import 'bootstrap';
import 'popper.js';

import '@fortawesome/fontawesome-free/js/all.js';

global.app_base = '/adierazleak';

$(function() {
    global.locale = $('html').attr("lang");
    $('.js-back').on('click', function(e) {
        e.preventDefault();
        var url = e.currentTarget.dataset.url;
        document.location.href = url;
    });
    $('[data-bs-toggle="tooltip"]').tooltip();
});