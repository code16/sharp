<script>
    import TextInput from './TextInput';
    export default {
        name: 'SharpPassword',
        functional: true,
        components: {
            [TextInput.name]:TextInput
        },
        render(h, ctx) {
            ctx.data.props.inputType = 'password';
            return h(TextInput.name, ctx.data);
        }
    }
</script>