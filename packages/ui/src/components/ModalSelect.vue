<template>
    <div>
        <slot :on="{ click:handleButtonClicked }" />

        <Modal :visible.sync="modalVisible" :title="title" :ok-title="okTitle" ok-only @ok="handleModalOkClicked">
            <div class="list-group" role="menu">
                <template v-for="option in options">
                    <div class="list-group-item list-group-item-action"
                        :class="itemClass(option)"
                        style="cursor: pointer; outline-offset: 3px"
                        role="menuitemradio"
                        tabindex="0"
                        :aria-checked="isSelected(option)"
                        @click="handleOptionSelected(option)"
                        @keydown.enter.space="handleOptionSelected(option)"
                        :key="option.value"
                    >
                        <div class="overflow-hidden">
                            <div class="row align-items-center gx-3">
                                <template v-if="$scopedSlots['item-prepend']">
                                    <div class="col-auto">
                                        <slot name="item-prepend" :option="option" />
                                    </div>
                                </template>

                                <div class="col py-2">
                                    {{ option.label }}
                                </div>

                                <template v-if="isSelected(option)">
                                    <div class="col-auto py-2">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </Modal>
    </div>
</template>

<script>
    import Modal from "./Modal";

    export default {
        components: {
            Modal,
        },
        props: {
            value: [Number, String],
            /**
             * @type Array.<{{ value: string, label: string }}>
             */
            options: Array,
            title: String,
            okTitle: String,
        },
        data() {
            return {
                modalVisible: false,
                selected: this.value ?? null,
            }
        },
        methods: {
            itemClass(option) {
                return {
                    'active': this.isSelected(option),
                }
            },
            isSelected(option) {
                return option.value === this.selected;
            },
            handleOptionSelected(option) {
                this.$emit('select', option);
                this.selected = option.value;
            },
            handleButtonClicked() {
                this.modalVisible = true;
            },
            handleModalOkClicked() {
                this.$emit('input', this.selected);
                this.$emit('change', this.selected);
            }
        },
    }
</script>
