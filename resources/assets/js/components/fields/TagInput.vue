<script>
    import Select from './Select';

    export default {
        name:'SharpTagInput',
        functional:true,
        render(h) {
            h(Select.name, {
                props:{
                    taggable:true
                }
            });
        }
    }
</script>