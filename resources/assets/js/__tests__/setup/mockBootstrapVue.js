import Vue from 'vue';

const mockPlugin = {
    install(Vue) {
        Vue.mixin({
            created() {
                if(typeof this.safeId === 'function') {
                    this.safeId = jest.fn(prepend => `${prepend||''}mocked`);
                }
            }
        })
    }
};

Vue.use(mockPlugin);