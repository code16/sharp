<template>
    <div>
        Autocomplete
    </div>
</template>

<script>
//    mode*: String. local ou remote
//    local_values: Array. Obligatoire si mode local.
//        remote_endpoint: String. Obligatoire si mode remote.
//        remote_method: String. GET (défaut) ou POST.
//        remote_search_attribute: String. Nom de l'attribut envoyé pour la recherche. Défaut : query.
//    item_key_attribute*: String. Attribut identifiant de l'item (pour valuer le champ)
//    list_item_template*: String (vue template). Template d'un item en mode searching.
//    result_item_template*: String (vue template). Template de l'item en mode valuated.
//    search_min_char: Number (int). Saisie minimale avant le déclenchement de la recherche. Défaut : 1.
    export default {
        props: {
            mode: {
                type: String
            },
            localValues: {
                type: Array
            },
            remoteEndPoint: {
                type: String
            },
            remoteMethod: {
                type: String
            },
            remoteSearchAttribute: {
                type: String,
                default: 'query'
            },
            itemKeyAttribute: {
                type: String
            },
            listItemTemplate: {
                type: String
            },
            searchMinBar: {
                type: Number,
                default: 1
            }
        },
        mounted() {

        }
    }
</script>