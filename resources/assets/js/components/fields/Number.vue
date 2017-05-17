<script>
    import Text from './Text';

    export default {
        name: 'SharpNumber',
        functional: true,
        components: {
            [Text.name]:Text
        },
        render(h, {props: {step, min, max, showControls}, data }) {
            return h(Text.name, {
                'class': {
                    'hide-controls':!showControls, ...data['class']
                },
                props: {
                    inputType:'number', ...data.props
                },
                attrs: {
                    step, min, max, ...data.attrs
                }
            });
        }
    }
</script>