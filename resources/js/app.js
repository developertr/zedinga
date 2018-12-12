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

Vue.component('login-register-forgot', require('./components/Auth/LoginRegisterForgot'));

const app = new Vue({
    el: '#app',
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
    }
});
