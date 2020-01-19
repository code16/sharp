<script>
    import { ErrorNode } from 'sharp/mixins';
    import FieldsLayout from '../../ui/FieldsLayout';

    export default {
        name:'SharpListItem',
        extends: FieldsLayout,
        mixins: [ErrorNode]
    }
</script>