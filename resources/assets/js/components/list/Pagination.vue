<script>
    import { ignoreWarns } from '../../util';
    import Pagination from 'bootstrap-vue/es/components/pagination/pagination';

    // for props/events check
    // https://bootstrap-vue.js.org/docs/components/pagination

    export default {
        name: 'SharpPagination',
        functional: true,

        render(h,ctx) {
            ctx.data.attrs.firstText = `<i class="fa fa-angle-double-left" aria-hidden="true"></i>`;
            ctx.data.attrs.prevText = `<i class="fa fa-angle-left" aria-hidden="true"></i>`;
            ctx.data.attrs.nextText = `<i class="fa fa-angle-right" aria-hidden="true"></i>`;
            ctx.data.attrs.lastText = `<i class="fa fa-angle-double-right" aria-hidden="true"></i>`;

            ctx.data.staticClass = 'SharpPagination';

            return h({
                name:'SharpPagination',
                extends: Pagination,

                watch: {
                    numberOfPages: {
                        immediate:true,
                        handler(n) {
                            if(!ctx.props.minPageEndButtons)return;
                            // Hide first/last buttons if number of pages inf than 3
                            ignoreWarns(() => this.hideGotoEndButtons = n<ctx.props.minPageEndButtons);
                        }
                    }
                }
            }, ctx.data, ctx.children);
        }
    }
</script>