import {createRouter, createWebHistory}  from 'vue-router'


const routes = [
    {
        path:'/',
        name:"home",
        component:()=>import( '../pages/home.vue'),
        meta: {
            redirectIfAuthenticated: false,
            title:'Home'
        },

    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('../pages/404.vue'),
        meta: {
            title: 'Page Not Found',
        },
    }
]


const router = createRouter({
    history:createWebHistory(),
    routes
})



export default router
