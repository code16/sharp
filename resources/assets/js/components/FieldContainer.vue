<template>
    <div>
        <label>{{label}}</label>
        <sharp-field :field-type="fieldType"
               :field-props="fieldProps"
               :field-attrs="attributes"
               :disabled="readOnly">
        </sharp-field>
        <div>{{helpMessage}}</div>
    </div>
</template>

<script>
    //    key*: String. Obligatoire ; identifiant du champ = name posté.
    //        label: String. Affiché en label, si présent.
    //        help_message: String. Affiché si présent sous le champ.
    //        attributes: Array. Attributs HTML à ajouter au champ.
    //        conditional_display: String. Condition d'affichage d'un champ du formulaire en fonction d'un autre, écrite sous la forme [!]fieldname[:values], avec
    //    fieldname la key d'un champ du formulaire, qui peut être de type Check, Dropdown, Autocomplete ou Ref.
    //    values la liste des valeurs possibles, séparée par uen virgule. Dans le cas d'un check, cette précision est inuntile.
    //    Si un "!" est placé en tête, la condition est inversée.
    //        conditional_display: "is_opened"
    //    conditional_display: "!is_opened"
    //    conditional_display: "colors:red,blue"
    //    readonly: Boolean. Si true, le champ est présenté en lecture seule, avec les implications que cela a pour chacun

    import Field from './Field';

    export default {
        props : {
            fieldKey: {
                type: String,
                required: true
            },
            fieldType: {
                type: String
            },
            fieldProps: {
                type: Object
            },
            label: {
                type: String
            },
            attributes: {
                type: Array
            },
            helpMessage: {
                type: Array
            },
            readOnly: {
                type: Boolean
            }
        },
        components: {
            'sharp-field':Field
        }
    }
</script>