<script>
    import Text from './Text';
    export default {
        name: 'SharpPassword',
        functional: true,
        components: {
            [Text.name]:Text
        },
        render(h, ctx) {
            ctx.data.props.inputType = 'password';
            return h(Text.name, ctx.data);
        }
    }
</script>