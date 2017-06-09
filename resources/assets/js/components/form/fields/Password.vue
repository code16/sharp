<script>
    import Text from './Text';

    export default {
        name: 'SharpPassword',
        functional: true,
        render(h, {data}) {
            data.props.inputType = 'password';
            return h(Text.name, data);
        }
    }
</script>