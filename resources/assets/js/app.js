var moment = require('moment');
var Vue = require('vue');
var Resource = require('vue-resource');
Vue.use(Resource);
Vue.config.debug = true;
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

import SearchIndex from './components/SearchIndex.vue';
import SearchCreate from './components/SearchCreate.vue';
import SearchShow from './components/SearchShow.vue';
import SearchActionsButtons from './components/SearchActionsButtons.vue';

if (
    document.body.classList.contains('searches-index') ||
    document.body.classList.contains('searches-show') ||
    document.body.classList.contains('searches-create')
) {

    Vue.filter('searchStatus', function (value) {
        if (value === false) {
            return '<i class="fa fa-spinner fa-spin"></i><span class="hidden-xs"> Running</span>';
        }

        return '<i class="fa fa-check"></i><span class="hidden-xs"> Finished</span>';
    });

    Vue.filter('formatInteger', function (value) {
        return value.toLocaleString();
    });

    Vue.filter('timeAgo', function (value) {
        return moment(value).fromNow();
    });

    new Vue({
        el: '#app',
        data: {},
        methods: {},
        components: {
            SearchIndex,
            SearchCreate,
            SearchShow
        }
    });

}