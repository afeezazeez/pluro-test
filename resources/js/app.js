import './bootstrap';
import {createApp} from 'vue'


import router from './src/router/index.js'

import App from './App.vue'
createApp(App)
    .use(router)
    .mount("#app")
