require('./bootstrap');
window.Vue = require('vue');

import axios from 'axios';

import Snotify, { SnotifyPosition } from 'vue-snotify';
Vue.use(Snotify, {
    toast: {
        position: SnotifyPosition.rightBottom,
        timeout: 5000
    }
});
import "vue-snotify/styles/material.css";

// Import component
import Loading from 'vue-loading-overlay';
// Import stylesheet
import 'vue-loading-overlay/dist/vue-loading.css';

Vue.component('login-register-forgot', require('./components/Auth/LoginRegisterForgot'));

const app = new Vue({
    el: '#app',
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
