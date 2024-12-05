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
    }
]


const router = createRouter({
    history:createWebHistory(),
    routes
})



export default router
