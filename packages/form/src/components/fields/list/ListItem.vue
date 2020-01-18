<script>
    import { ErrorNode } from 'sharp/mixins';
    import FieldsLayout from '../../FieldsLayout';

    export default {
        name:'SharpListItem',
        extends: FieldsLayout,
        mixins: [ErrorNode]
    }
</script>