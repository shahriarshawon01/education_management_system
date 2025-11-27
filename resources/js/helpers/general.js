import axios from 'axios';

export function initialize(store, router) {
    router.beforeEach((to, from, next) => {
        window.document.title = to.meta.pageTitle !== undefined ? to.meta.pageTitle : '';
        store.state.dataList = {};
        store.state.detailsData = {};
        store.state.formObject = {
            school_id: getCookie('school_id'),
        };

        store.state.formType = 1;
        store.state.updateId = '';
        $('body').removeClass('vertical-sidebar-enable');

        store.formFilter = {
            school_id: getCookie('school_id'),
            keyword: '',
            perPage: 15,
            status: '',
        };
        next();
    });

    if (localStorage.getItem('software')) {
        store.state.software = localStorage.getItem('software');
        axios.defaults.headers.common['Software'] = localStorage.getItem('software');
    }

    axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
    axios.interceptors.response.use(function (response) {
        return response;
    }, function (error) {
        if (parseInt(error.response.status) === 401) {
            // store.commit('logout');
        }
        return Promise.reject(error);
    });

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    // user activity log
    axios.interceptors.request.use(function (config) {
        var logData = {
            url: config.url,
            method: config.method,
            module: router.currentRoute.meta.pageTitle,
        };
        config.headers['LogData'] = JSON.stringify(logData);
        return config;
    }, (err) => {
        return Promise.reject(err);
    }
    );
}
