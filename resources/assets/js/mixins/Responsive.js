import debounce from 'lodash/debounce';

export default function (breakpoint='sm') {
    return {
        data() {
            return {
                isViewportSmall: false,
            }
        },
        methods: {
            $_responsiveUpdate() {
                let { offsetWidth } = this.$_testElm;
                this.isViewportSmall = !!offsetWidth;
            }
        },
        created() {
            let id = `viewport-down-${breakpoint}`;
            this.$_testElm = document.getElementById(id);

            if(!this.$_testElm) {
                this.$_testElm = document.createElement('div');
                this.$_testElm.id = id;
                this.$_testElm.classList.add(`d-${breakpoint}-none`);
                document.body.appendChild(this.$_testElm);
            }

            this.$_responsiveUpdate();
            this.$_debouncedRespnsiveUpdate = debounce(this.$_responsiveUpdate, 300);
            window.addEventListener('resize', this.$_debouncedRespnsiveUpdate);
        },
        destroyed() {
            window.removeEventListener('resize', this.$_debouncedRespnsiveUpdate);
        }
    }
}