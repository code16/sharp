export const UploadModifiers = {
    props: {
        compactedThumbnail: Boolean
    },
    computed: {
        modifiers() {
            return {
                compacted: this.compactedThumbnail
            }
        }
    }
};

export const VueClipModifiers = {
    props: {
        modifiers: {
            type: Object,
            default: () => {}
        }
    },
    computed: {
        modifiersClasses() {
            return {
                'SharpUpload--compacted': this.modifiers.compacted
            }
        }
    }
};