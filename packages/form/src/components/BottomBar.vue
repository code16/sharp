<script setup lang="ts">
    import { __ } from "@/util/i18n";
</script>

<template>
    <div class="position-sticky bottom-0 px-4 py-3 bg-white border-top"
        :class="{ 'shadow': stuck }"
        v-sticky
        @stuck-change="stuck = $event.detail"
        style="z-index: 100; transition: box-shadow .25s ease-in-out"
    >
        <div class="row justify-content-end align-items-center gx-3">
            <div class="col">
                <slot name="left"></slot>
            </div>
            <div class="col-auto">
                <Button outline @click="handleCancelClicked">
                    <template v-if="showBackButton">
                        {{ __('sharp::action_bar.form.back_button') }}
                    </template>
                    <template v-else>
                        {{ __('sharp::action_bar.form.cancel_button') }}
                    </template>
                </Button>
            </div>
            <template v-if="showSubmitButton">
                <div class="col-auto">
                    <Button style="min-width: 6.5em" :disabled="uploading || loading" @click="handleSubmitClicked">
                        <template v-if="uploading">
                            {{ __('sharp::action_bar.form.submit_button.pending.upload') }}
                        </template>
                        <template v-else-if="create">
                            {{ __('sharp::action_bar.form.submit_button.create') }}
                        </template>
                        <template v-else>
                            {{ __('sharp::action_bar.form.submit_button.update') }}
                        </template>
                    </Button>
                </div>
            </template>
        </div>
    </div>
</template>

<script lang="ts">
import {Button} from "@sharp/ui";
import { sticky } from "sharp/directives";

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
        handleCancelClicked() {
            this.$emit('cancel');
        },
        handleSubmitClicked() {
            this.$emit('submit');
        },
        handleDeleteClicked() {
            this.$emit('delete');
        },
    },
    directives: {
        sticky
    }
}
</script>
