<template>
    <div>
        <button class="btn btn-outline-light" @click="modalVisible=true">
            Search
        </button>

        <Modal :visible.sync="modalVisible" hide-footer  @shown="$refs.input.focus()">
            <input type="text" class="form-control" placeholder="Search..." ref="input">
        </Modal>
    </div>
</template>

<script>
import { Modal } from "sharp-ui";

export default {
    components: {
        Modal
    },
    data() {
        return {
            modalVisible: false
        }
    },
}
</script>



