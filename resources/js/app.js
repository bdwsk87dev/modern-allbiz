require('./bootstrap');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';

Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
const app = document.getElementById('app');
new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
// Pagination
Vue.component('pagination', require('laravel-vue-pagination'));
// Font-awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCogs, faPlay, faUserTimes } from '@fortawesome/free-solid-svg-icons'
library.add(faCogs, faPlay, faUserTimes)
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
Vue.component('font-awesome-icon', FontAwesomeIcon)

