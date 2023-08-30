import { computed, ref } from "vue/dist/vue";


export function useReorderingLists() {
    const reorderingEntityLists = ref({});
    const isReordering = computed(() => Object.values(reorderingEntityLists.value).some(reordering => reordering));
    function onEntityListReordering(key: string, reordering: boolean) {
        reorderingEntityLists.value[key] = reordering;
    }
    return {
        isReordering,
        onEntityListReordering,
    }
}
