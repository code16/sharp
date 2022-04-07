import { Dropdown, DropdownItem } from "./components";

export default function(Vue, { store, router }) {
    Vue.component('sharp-dropdown', Dropdown);
    Vue.component('sharp-dropdown-item', DropdownItem);
}

export * from './components';
export * from './util';
