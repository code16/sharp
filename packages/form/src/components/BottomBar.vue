<template>
    <div class="position-sticky bottom-0 p-3 bg-white border-top"
        :class="{ 'shadow': stuck }"
        v-sticky
        @stuck-change="stuck = $event.detail"
        style="z-index: 100; transition: box-shadow .25s ease-in-out"
    >
        <div class="row justify-content-end gx-3">
            <div class="col-auto">
                <Button outline @click="handleCancelClicked">
                    <template v-if="showBackButton">
                        {{ lang('action_bar.form.back_button') }}
                    </template>
                    <template v-else>
                        {{ lang('action_bar.form.cancel_button') }}
                    </template>
                </Button>
            </div>
            <template v-if="showSubmitButton">
                <div class="col-auto">
                    <Button :disabled="uploading || loading" @click="handleSubmitClicked">
                        <template v-if="uploading">
                            {{ lang('action_bar.form.submit_button.pending.upload') }}
                        </template>
                        <template v-else-if="create">
                            {{ lang('action_bar.form.submit_button.create') }}
                        </template>
                        <template v-else>
                            {{ lang('action_bar.form.submit_button.update') }}
                        </template>
                    </Button>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import {Button} from "sharp-ui";
import { sticky } from "sharp/directives";
import { lang } from "sharp";

export default  {
    components: {
        Button,
    },
    props: {
        showSubmitButton: Boolean,
        showDeleteButton: Boolean,
        showBackButton: Boolean,
        create: Boolean,
        uploading: Boolean,
        loading: Boolean,
        breadcrumb: Array,
        showBreadcrumb: Boolean,
        hasDeleteConfirmation: Boolean,
    },
    data() {
        return {
            stuck: false,
        }
    },
    methods: {
        lang,
        handleCancelClicked() {
            this.$emit('cancel');
        },
        handleSubmitClicked() {
            this.$emit('submit');
        }
    },
    directives: {
        sticky
    }
}
</script>
