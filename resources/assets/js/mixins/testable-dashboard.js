import * as testDashboard from '../_test-dashboard';

export default {
    props: {
        test: Boolean
    },
    mounted() {
        if(this.test) {
            this.mount(testDashboard);

            this.ready = true;
        }
    }
}