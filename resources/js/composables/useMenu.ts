import { MenuData, MenuItemData, UserMenuData } from "@/types";
import { usePage } from "@inertiajs/vue3";

class Menu implements MenuData {
    items: Array<MenuItemData>;
    userMenu: UserMenuData;
    isVisible: boolean;

    constructor(menu: MenuData) {
        Object.assign(this, menu);
    }

    getEntityItem(entityKey: string | null): MenuItemData | null {
        return entityKey
            ? this.items
                .map(item => [item, item.children])
                .flat(2)
                .filter(Boolean)
                .find((item: MenuItemData) => item.entityKey === entityKey)
            : null
    }
}

export default function useMenu() {
    return new Menu(usePage().props.menu as MenuData);
}
