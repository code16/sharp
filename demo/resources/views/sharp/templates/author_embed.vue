
<div class="row gx-3">
    <template v-if="picture">
        <div class="col-auto">
            <img :src="picture.thumbnail" width="50" height="50">
        </div>
    </template>
    <template>
        <div class="col">
            <div><strong>{{ name }}</strong></div>
            <div style="font-size: .875rem">
                <slot></slot>
            </div>
        </div>
    </template>
</div>
