import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter);
import App from './App'

import Vuex from 'vuex';
Vue.use(Vuex);

import VeeValidate from 'vee-validate';

Vue.use(VeeValidate, {
    events: 'input',
    fieldsBagName: '',
});

import datepicker from './plugins/datepicker';

Vue.component('datepicker', datepicker);

import '../../public/css/select2_custom.css';
import select2 from './directives'
Vue.directive('select2', select2);
Vue.filter('truncate', function (value, limit) {
    if (!value) return '';
    if (value.length <= limit) return value;
    return value.substring(0, limit) + '...';
});

import tinymce from 'vue-tinymce-editor';
Vue.component('tinymce', tinymce);

import StoreData from './store';
const store = new Vuex.Store(StoreData);

import autocomplete from './plugins/autocomplete'
Vue.component('autocomplete', autocomplete);

import BaseMixin from './mixins/base_mixin';
import HttpMixin from './mixins/http_mixin';
Vue.mixin(BaseMixin);
Vue.mixin(HttpMixin);

import index from './routes';

var allRoutes = [];
const routes = allRoutes.concat(index);

const router = new VueRouter({
    mode: 'history',
    routes: routes,
    linkActiveClass: "active",
});

import VueTagsInput from '@johmun/vue-tags-input';
Vue.component('VueTagsInput', VueTagsInput);

import pagination from './plugins/pagination/pagination'
Vue.component('pagination', pagination);

import VueApexCharts from 'vue-apexcharts';
Vue.component('apexchart',VueApexCharts);

import VueToastr from '@deveodk/vue-toastr';
import '@deveodk/vue-toastr/dist/@deveodk/vue-toastr.css';
Vue.use(VueToastr, {
    defaultPosition: 'toast-bottom-right',
    defaultType: 'info',
    defaultTimeout: 3000
});

import 'sweetalert2/dist/sweetalert2.min.css';
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

import axios from 'axios';
import VueAxios from 'vue-axios'
Vue.use(VueAxios, axios);

import {initialize} from './helpers/general';
initialize(store, router);


const app = new Vue({
    el: '#app',
    components: { App },
    router,store,axios
});
