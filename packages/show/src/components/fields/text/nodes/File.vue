<template>
    <ShowFile
        class="embed"
        :value="value"
        :root="false"
    />
</template>

<script>
    import { filesEquals, parseFilterCrop, parseFilterRotate } from "sharp-files";
    import ShowFile from "../../File.vue";

    export default {
        components: {
            ShowFile,
        },
        inject: [
            'state',
        ],
        props: {
            name: String,
            path: String,
            disk: String,
            filterCrop: String,
            filterRotate: String,
        },
        computed: {
            value() {
                const value = {
                    name: this.name,
                    path: this.path,
                    disk: this.disk,
                }
                const file = this.state.files.find(file => filesEquals(file, value));
                return {
                    ...value,
                    ...file,
                }
            },
            filters() {
                const filters = {
                    crop: parseFilterCrop(this.filterCrop),
                    rotate: parseFilterRotate(this.filterRotate),
                };

                if(Object.values(filters).every(filter => filter == null)) {
                    return null;
                }

                return filters;
            },
        },
        created() {
            this.state.files.push({
                name: this.name,
                path: this.path,
                disk: this.disk,
                filters: this.filters,
            });
        },
    }
</script>
