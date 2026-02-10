
// import 'bootstrap/dist/css/bootstrap.min.css'
// import 'bootstrap'
import '../css/app.css'; // importe tout le CSS compilé

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import i18n from './i18n'
// import { InertiaProgress } from '@inertiajs/progress'

// Active la barre de progression lors de la navigation
// InertiaProgress.init()

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(i18n)
            .mount(el)
    },
})
