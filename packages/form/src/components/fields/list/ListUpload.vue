<template>
    <div class="card position-relative SharpListUpload text-muted"
        :class="{ 'SharpListUpload--active': dragActive }"
    >
        <div class="card-body d-flex align-items-center justify-content-center">
            <div class="SharpListUpload__text">
                <div class="row align-items-center gx-0">
                    <div class="col-auto">
                        <svg class="SharpListUpload__icon" width="2em" height="2em" viewBox="0 0 32 32">
                            <path d="M26,24v4H6V24H4v4H4a2,2,0,0,0,2,2H26a2,2,0,0,0,2-2h0V24Z"/><polygon points="26 14 24.59 12.59 17 20.17 17 2 15 2 15 20.17 7.41 12.59 6 14 16 24 26 14"/>
                        </svg>
                    </div>
                    <div class="col">
                        <div v-html="text" @click.prevent="handleTextClicked"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="SharpListUpload__help">
            {{ helpText }}
        </div>
        <input
            class="SharpListUpload__input"
            type="file"
            :aria-label="label"
            multiple
            @change="handleChanged"
            @dragenter="handleDragEnter"
            @dragleave="handleDragLeave"
            @drop="handleDrop"
            ref="input"
        >
    </div>
</template>

<script>
    import { lang } from "sharp";

    export default {
        props: {
            limit: Number,
        },
        data() {
            return {
                dragActive: false,
            }
        },
        computed: {
            text() {
                return this.getText({
                    link: '<a href="#" class="text-reset" tabindex="-1">$1</a>',
                });
            },
            label() {
                return this.getText({
                    link: '$1',
                });
            },
            helpText() {
                return lang('form.list.bulk_upload.help_text')
                    .replace(':limit', this.limit);
            },
        },
        methods: {
            getText({ link }) {
                return lang('form.list.bulk_upload.text')
                    .replace(/\[(.+?)]\(.*?\)/, link);
            },
            handleDragEnter() {
                this.dragActive = true;
            },
            handleDragLeave() {
                this.dragActive = false;
            },
            handleDrop() {
                this.dragActive = false;
            },
            handleTextClicked() {
                this.$refs.input.click();
            },
            handleChanged(e) {
                this.$emit('change', e);
                e.target.value = '';
            }
        }
    }
</script>
