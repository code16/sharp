<template>
    <div>
        <div class="editor-toolbar">
            <template v-for="part in toolbar">
                <i v-if="part==='|'" class="separator">|</i>
                <a v-else-if="buttons[part]"
                    class="fa"
                    :class="buttons[part].icon"
                    v-button-data="buttons[part]"
                >
                </a>
            </template>
        </div>
        <div class="trix-dialogs" data-trix-dialogs>
            <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
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
    import { LocalizationBase } from "../../../../mixins";

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
        directives: {
            buttonData(el, { value }) {
                let { attribute, action } = value;
                attribute && (el.dataset.trixAttribute = attribute);
                action && (el.dataset.trixAction = action);
            }
        }
    }
</script>