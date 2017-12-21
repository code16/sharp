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
        mounted() {
            let id = `viewport-down-${breakpoint}`;
            this.$_testElm = document.getElementById(id);

            if(!this.$_testElm) {
                this.$_testElm = document.createElement('div');
                this.$_testElm.id = id;
                this.$_testElm.classList.add(`d-${breakpoint}-none`);
                document.body.appendChild(this.$_testElm);
            }

            this.$_responsiveUpdate();
            window.addEventListener('resize', this.$_responsiveUpdate);
        },
        destroyed() {
            window.removeEventListener('resize', this.$_responsiveUpdate);
        }
    }
}