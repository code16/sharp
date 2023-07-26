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
<!--                <template v-if="showDeleteButton">-->
<!--                    <Button class="d-flex align-items-center border-0" variant="danger" outline :disabled="loading" @click="handleDeleteClicked">-->
<!--                        <svg class="d-block me-1" width="16" height="16" viewBox="0 0 20 20">-->
<!--                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />-->
<!--                        </svg>-->
<!--                        <span>-->
<!--                            {{ lang('action_bar.form.delete_button') }}-->
<!--                        </span>-->
<!--                    </Button>-->
<!--                    <a class="text-danger fs-7" :class="{ 'disabled':loading }" href="#"  @click="handleDeleteClicked">-->
<!--                        {{ lang('action_bar.form.delete_button') }}-->
<!--                    </a>-->
<!--                </template>-->
            </div>
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
                    <Button style="min-width: 6.5em" :disabled="uploading || loading" @click="handleSubmitClicked">
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
import {Button} from "@sharp/ui";
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
