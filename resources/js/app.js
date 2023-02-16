import { createApp } from 'vue';
import App from './views/App';
import axios from 'axios';
import VueAxios from 'vue-axios';
import "@fontsource/open-sans";
import * as VueRouter  from 'vue-router';

import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';

import AppSwitcher from "./views/AppSwitcher.vue";

import KpiRoute from "./views/kpi/KpiRoute.vue";
import KpiAdminView from "./views/kpi/AdminView.vue";
import KpiManagerView from "./views/kpi/ManagerView.vue";
import KpiWorkerView from "./views/kpi/WorkerView.vue";

import ScheduleRoute from "./views/schedule/ScheduleRoute.vue";
import ScheduleAdminView from "./views/schedule/AdminView.vue";
import ScheduleManagerView from "./views/schedule/ManagerView.vue";
import ScheduleWorkerView from "./views/schedule/WorkerView.vue";

const routes = [
    { path: '', component: AppSwitcher },

    { path: '/schedule', component: ScheduleRoute },
    { path: '/schedule/admin', component: ScheduleAdminView },
    { path: '/schedule/manager', component: ScheduleManagerView},
    { path: '/schedule/worker', component: ScheduleWorkerView},

    { path: '/kpi', component: KpiRoute },
    { path: '/kpi/admin', component: KpiAdminView },
    { path: '/kpi/manager', component: KpiManagerView },
    { path: '/kpi/worker', component: KpiWorkerView },
]

const router = VueRouter.createRouter({
    mode: 'history',
    history: VueRouter.createWebHashHistory(),
    routes,
})


let app = createApp(App);

app.use(router);
app.use(VueAxios, axios);
app.use(Antd);

let baseUrl = 'http://schedule'
app.use({
    install (app){
        app.config.globalProperties.$kpiApi = axios.create({
            baseURL: baseUrl +'/app/kpi',
            headers: {
                // common:{
                //     'User-Data': app_settings['USER_DATA']
                // }
            }
        });
        app.config.globalProperties.$scheduleApi = axios.create({
            baseURL: baseUrl + '/app/schedule',
            headers: {
                // common:{
                //     'User-Data': app_settings['USER_DATA']
                // }
            }
        });
    }
});

app.mount('#app');
