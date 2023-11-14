import DefaultTheme from 'vitepress/theme'
import './style.css';
import type {Theme} from "vitepress";
import { onMounted, watch, nextTick } from 'vue';
import { useRoute } from 'vitepress';
import mediumZoom from 'medium-zoom';

export default {
    extends: DefaultTheme,
    setup() {
        const route = useRoute();
        const initZoom = () => {
            new mediumZoom('.content img', { background: 'var(--vp-c-bg)' });
        };
        onMounted(() => {
            initZoom();
        });
        watch(
            () => route.path,
            () => nextTick(() => initZoom())
        );
    },
} satisfies Theme;
