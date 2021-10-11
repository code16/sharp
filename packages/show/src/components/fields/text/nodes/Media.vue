<template>
    <ShowFile
        :value="value"
        :root="false"
    />
</template>

<script>
    import { filesEquals } from "sharp-files";
    import ShowFile from "../../File";

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
        },
        created() {
            this.state.files.push({
                name: this.name,
                path: this.path,
                disk: this.disk,
            });
        },
    }
</script>
