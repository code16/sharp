import DefaultTheme from 'vitepress/theme'
import type {Theme} from "vitepress";
import { onMounted, watch, nextTick } from 'vue';
import { useRoute } from 'vitepress/client';
import mediumZoom from 'medium-zoom';
import VersionNavMenu from './components/VersionNavMenu.vue';


import './style.css';
import './home.css';
import { getCurrentVersionForRoute } from "./utils/getCurrentVersionForRoute";

export default {
    extends: DefaultTheme,
    setup() {
        const route = useRoute();
        const initZoom = () => {
            mediumZoom('.content img', { background: 'var(--vp-c-bg)' });
        };
        const updateTitle  = () => {
            const version = getCurrentVersionForRoute(route);
            document.title = document.title.replace(/\| Sharp/, `| Sharp ${version?.name ?? ''}`);
        }
        onMounted(() => {
            initZoom();
            updateTitle();
        });
        watch(
            () => route.path,
            () => {
                updateTitle();
                nextTick(() => initZoom());
            }
        );
    },
    enhanceApp({ app }) {
        app.component('VersionNavMenu', VersionNavMenu);
    },
} satisfies Theme;
