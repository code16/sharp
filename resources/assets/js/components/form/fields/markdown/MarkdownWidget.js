export default function($parent) {
    return {
        beforeCreate() {
            this.$parent = $parent;
        }
    };
}