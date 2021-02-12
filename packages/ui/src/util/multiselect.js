

export async function multiselectUpdateScroll(vm) {
    await new Promise(r => setTimeout(r, 10));
    const content = vm.$el.querySelector('.multiselect__content');
    const rect = content.getBoundingClientRect();
    if (rect.bottom > window.innerHeight) {
        window.scrollBy({ top: rect.bottom - window.innerHeight + 20, behavior:'smooth' });
    }
}
