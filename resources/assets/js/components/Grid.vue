<template>
    <div class="SharpGrid">
        <div v-for="(row,i) in rows" class="SharpGrid__row row">
            <div v-for="(col,j) in row" :class="colClass[i][j]" class="SharpGrid__col">
                <slot v-bind="col"></slot>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name:'SharpGrid',

        props: {
            rows: { // 2D array [ligne][col]
                type: Array,
                required: true
            }
        },
        computed : {
            colClass() {
                let res=[];
                for(let i=0;i<this.rows.length;i++) {
                    res[i] = [];
                    let equals=false;
                    for(let j=0;j<this.rows[i].length;j++) {
                        let col = this.rows[i][j];
                        if(equals || !col.size) {
                            if(!equals) {
                                res[i].fill('col-md');
                                equals = true;
                            }
                            res[i][j] = 'col-md';
                        }
                        else {
                            res[i][j] = `col-md-${col.size}`;
                            if(col.sizeXS) {
                                res[i][j] += ` col-${col.sizeXS}`;
                            }
                        }
                    }
                }
                return res;
            }
        },
    }
</script>