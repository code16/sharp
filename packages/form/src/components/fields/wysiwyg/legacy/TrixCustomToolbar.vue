<template>
    <div>
        <div>
            <template v-for="group in toolbarGroups">
                <div class="btn-group">
                    <template v-for="part in group">
                        <button class="btn btn-light" v-button-data="buttons[part]">
                            <i :class="buttons[part].icon"></i>
                        </button>
                    </template>
                </div>
            </template>
        </div>
        <div class="trix-dialogs" data-trix-dialogs>
            <div class="trix-dialog trix-dialog--link border-0 shadow" data-trix-dialog="href" data-trix-dialog-attribute="href">
                <div class="trix-dialog__link-fields">
                    <input type="url" name="href" class="trix-input trix-input--dialog" :placeholder="lSub('dialogs.add_link.input_placeholder')" required data-trix-input>
                    <div class="trix-button-group">
                        <input type="button" class="trix-button trix-button--dialog" :value="lSub('dialogs.add_link.link_button')" data-trix-method="setAttribute">
                        <input type="button" class="trix-button trix-button--dialog" :value="lSub('dialogs.add_link.unlink_button')" data-trix-method="removeAttribute">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { buttons } from "./config";
    import { LocalizationBase } from "sharp/mixins";

    export default {
        mixins:[ LocalizationBase('form.wysiwyg') ],
        props: {
            toolbar: Array,
        },
        data() {
            return {
                buttons
            }
        },
        computed: {
            toolbarGroups() {
                return this.toolbar.reduce((res, btn) => {
                    if(btn === '|') {
                        return [...res, []];
                    }
                    res[res.length - 1].push(btn);
                    return res;
                }, [[]]);
            },
        },
        directives: {
            buttonData(el, { value }) {
                let { attribute, action } = value;
                attribute && el.setAttribute('data-trix-attribute', attribute);
                action && el.setAttribute('data-trix-action', action);
            },
        }
    }
</script>
