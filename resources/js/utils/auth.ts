import { UserData } from "@/types";
import { usePage } from "@inertiajs/vue3";

export function auth() {
    return usePage().props.auth as { user: UserData };
}
