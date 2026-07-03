import DefaultTheme from 'vitepress/theme'
import type {Theme} from "vitepress";
import Layout from "./Layout.vue";

// @ts-ignore
import './style.css';
// @ts-ignore
import './home.css';


export default {
    extends: DefaultTheme,
    Layout: Layout,
} satisfies Theme;
