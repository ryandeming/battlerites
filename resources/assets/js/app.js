
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
    el: '#app'
});

$(function() {

    //slide down match history
    $('.sidebar .match').on('click', function() {
        
        if(!$(this).hasClass('open')) {
            $('.match').removeClass('open');
            $('.match-stats-box').slideUp();
            $('.expand').text('Click to expand');
            $(this).find('.match-stats-box').slideDown();
            $(this).find('.expand').text('Click to shrink');
            $(this).addClass('open');
        } else {
            $('.match-stats-box').slideUp();
            $(this).removeClass('open');
            $(this).find('.expand').text('Click to expand');
        }
        
    });

    $('.match-stats-box').on('click', function(e) {
		e.stopPropagation();
	});

    //lookup redirect
});

function lookupRedirect() {
    window.location.href ="http://battlerites.net/player/" + $('#playerName').val();
}