<template>
    <div>
        <slot :on="{ click:handleButtonClicked }" />

        <Modal
            :visible="modalVisible"
            :title="title"
            :ok-title="okTitle"
            ok-only
            :size="size"
            @ok="handleModalOkClicked"
            @change="handleVisibleChanged"
        >
            <div class="list-group" role="menu">
                <template v-for="option in options">
                    <div class="list-group-item list-group-item-action pe-2"
                        :class="itemClass(option)"
                        style="cursor: pointer; outline-offset: 4px"
                        role="menuitemradio"
                        tabindex="0"
                        :aria-checked="isSelected(option)"
                        @click="handleOptionSelected(option)"
                        @keydown.enter.space="handleOptionSelected(option)"
                        :key="option.value"
                    >
                        <div class="py-1">
                            <div class="row align-items-center gx-3">
                                <template v-if="$scopedSlots['item-prepend']">
                                    <div class="col-auto">
                                        <slot name="item-prepend" :option="option" />
                                    </div>
                                </template>

                                <div class="col">
                                    {{ option.label }}
                                </div>

                                    <div class="col-auto" :class="{ 'invisible': !isSelected(option) }">
                                        <div class="bg-primary text-inverted d-inline-flex rounded-circle justify-content-center align-items-center" style="width: 1.5em; height: 1.5em">
                                            <i class="fas fa-check fa-sm"></i>
                                        </div>
                                    </div>
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
            visible: Boolean,
            /**
             * @type Array.<{{ value: string, label: string }}>
             */
            options: Array,
            title: String,
            okTitle: String,
            size: String,
        },
        data() {
            return {
                modalVisible: this.visible,
                selected: this.value ?? null,
            }
        },
        watch: {
            visible(visible) {
                this.modalVisible = visible;
            },
        },
        methods: {
            itemClass(option) {
                return {
                    'active bg-white text-primary': this.isSelected(option),
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
            handleVisibleChanged(visible) {
                this.modalVisible = visible;
                this.$emit('update:visible', visible);
            },
            handleModalOkClicked() {
                this.$emit('input', this.selected);
                this.$emit('change', this.selected);
            }
        },
    }
</script>
