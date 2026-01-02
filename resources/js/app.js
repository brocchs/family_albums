import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import Loading from './Components/Loading.vue';
import { useLoading } from './composables/useLoading';
import { router } from '@inertiajs/vue3';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

// Global navigation loading
const { startLoading, stopLoading } = useLoading();

router.on('start', () => startLoading('Memuat halaman...'));
router.on('finish', () => stopLoading());

// Page reload loading
window.addEventListener('beforeunload', () => startLoading('Memuat ulang halaman...'));
window.addEventListener('load', () => stopLoading());

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .component('Loading', Loading)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
