<template>
    <SharpActionBar>
        <template slot="right">
            <template v-if="canEdit">
                <a :href="formUrl" class="SharpButton SharpButton--accent">
                    {{ l('action_bar.show.edit_button') }}
                </a>
            </template>
        </template>
    </SharpActionBar>
</template>

<script>
    import { Localization } from "../../mixins";
    import SharpActionBar from './ActionBar';
    import SharpCommandsDropdown from '../commands/CommandsDropdown';

    export default {
        mixins: [Localization],
        components: {
            SharpActionBar,
            SharpCommandsDropdown,
        },
        props: {
            commands: Array,
            formUrl: String,
            canEdit: Boolean,
        },
        methods: {
            handleEditButtonClicked() {
                this.$emit('edit');
            },
            handleCommandSelected(command) {
                this.$emit('command', command);
            },
        }
    }
</script>