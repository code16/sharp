<template>
    <div>  <!--TODO système grille-->
        <field-container v-for="field in fields"
                         :field-key="field.key"
                         :field-type="field.type"
                         :field-props="field.props"
                         :label="field.label"
                         :attributes="field.attributes"
                         :help-message="field.helpMessage"
                         :read-only="field.readOnly">
        </field-container>
    </div>
</template>

<script>
    import FieldContainer from './FieldContainer';

    export default {
        data() {
            return {
                fields:null
            }
        },
        components: {
            'field-container':FieldContainer
        },
        mounted() {
            // GET fields
            this.fields = [
                {
                    type:'sharp-autocomplete',
                    key:'name',
                    props: {

                    }
                }
            ]

        },
    }
</script>