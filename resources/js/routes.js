import Vue from 'vue';
import Router from 'vue-router';
import myProfileSettings from './components/Profile/Settings'
// import myProfileWall from './components/Profile/Wall';
// import About from './components/About.vue'

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/settings',
            name: 'myProfileSettings',
            component: myProfileSettings,
        },
        {
            path: '/',
            name: 'myProfileWall',
            component: Vue.component('myProfileWall',require('./components/Profile/Wall')),
        },
        {
            path: '/inga/:id',
            name: 'ingaDetail',
            component : Vue.component('IngaDetail',require('./components/Profile/Inga/Detail')),
            props : true
        }
        // {
        //     path: '/inga/:id',
        //     name: 'inga',
        //     component: ingaDetail,
        //     props: true
        // }
    ],
    scrollBehavior(to, from, savedPosition) {
        console.log(savedPosition);
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
});
