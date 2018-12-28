require('./bootstrap');
window.Vue = require('vue');

window.VueGoogleMaps = require('vue2-google-maps');
Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyAGTDzdpHzvHW_DEyQgRC_hA2-btaxypHs',
        libraries: 'places',
        language : 'tr'
    },
});

import Vue2TouchEvents from 'vue2-touch-events';
Vue.use(Vue2TouchEvents);

import VueCookies from 'vue-cookies';
Vue.use(VueCookies);
VueCookies.config('3h');

Vue.component('google-map', VueGoogleMaps.Map);
Vue.component('google-marker', VueGoogleMaps.Marker);

import axios from 'axios';
import router from './routes';

import Snotify, { SnotifyPosition } from 'vue-snotify';
Vue.use(Snotify, {
    toast: {
        position: SnotifyPosition.rightBottom,
        timeout: 5000
    }
});
import "vue-snotify/styles/material.css";

// time ago
import VueTimeago from 'vue-timeago';
Vue.use(VueTimeago, {
    name: 'Timeago', // Component name, `Timeago` by default
    locale: undefined, // Default locale
    locales: {
        'tr': require('date-fns/locale/tr'),
    }
});

// nl2br
import Nl2br from 'vue-nl2br';
Vue.component('nl2br', Nl2br);

// Import component
import Loading from 'vue-loading-overlay';
// Import stylesheet
import 'vue-loading-overlay/dist/vue-loading.css';

import {StarRating} from 'vue-rate-it';
import {HeartRating} from 'vue-rate-it';
import {FaRating} from 'vue-rate-it';
import {ImageRating} from 'vue-rate-it';

Vue.component('image-rating', ImageRating);


import VueYoutube from 'vue-youtube'
Vue.use(VueYoutube);

import vueVimeoPlayer from 'vue-vimeo-player'
import Vue from 'vue'

Vue.use(vueVimeoPlayer)

Vue.component('login-register-forgot', require('./components/Auth/LoginRegisterForgot'));
Vue.component('user-info',require('./components/User/Info'));
Vue.component('new-inga',require('./components/Profile/Inga/New'));
Vue.component('my-profile-ingas',require('./components/Profile/Inga/List'));

Vue.component('inga',require('./components/Inga/IngaPreview.vue'));
Vue.component('profile-preview-box',require('./components/User/ProfilePreviewBox'));

const app = new Vue({
    el: '#app',
    router,
    data: {
        isLoading: false,
        fullPage: true
    },
    components: {
        Loading
    },
    mounted() {
        // this.isLoading = true;
    },
    methods:{
        showErrors : function(errors) {
            var i = 0;
            var j = Object.keys(errors).length;
            for (i = 0; i < Object.keys(errors).length; i++) {
                var message = Object.values(errors)[j-1];
                this.$snotify.error(message[0]);
                j--;
            }
        },
        formatNumber(value) {
            let val = (value/1).toFixed(0).replace('.', ',');
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }
    },
    watch: {
    }
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
