<script>
    import Text from './Text';

    export default {
        name: 'SharpPassword',
        functional: true,
        render(h, ctx) {
            ctx.data.props.inputType = 'password';
            return h(Text.name, ctx.data);
        }
    }
</script>