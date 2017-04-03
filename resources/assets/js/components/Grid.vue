<template>
    <div>
        <el-row v-for="row in rows" :gutter="20">
            <el-col v-for="col in row" :span="col.size ? col.size*2 : 24/row.length">
                <slot :item="col"></slot>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import { Row, Col } from 'element-ui';

    export default {
        name:'SharpGrid',
        components: {
            [Row.name]:Row,
            [Col.name]:Col
        },

        props: {
            rows: { // 2D array [ligne][col]
                type: Array,
                required: true
            }
        }
    }
</script>