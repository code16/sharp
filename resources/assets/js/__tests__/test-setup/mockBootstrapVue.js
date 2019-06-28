import Vue from 'vue';

const mockPlugin = {
    install(Vue) {
        Vue.mixin({
            created() {
                if(this.localId_ !== 'mocked') {
                    Object.defineProperty(this, 'localId_', {
                        get:()=>'mocked'
                    });
                }
            }
        })
    }
};

process.env.BOOTSTRAP_VUE_NO_WARN = true;

Vue.use(mockPlugin);