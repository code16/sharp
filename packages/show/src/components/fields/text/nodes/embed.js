import { parseAttributeValue } from "sharp-embeds";
import { EmbedRenderer } from 'sharp-embeds';
import { ignoreVueElement } from "sharp";

export function createEmbedComponent(embedOptions) {
    return {
        name: `Embed_${embedOptions.tag}`,
        template: `
            <component :is="embedOptions.tag" class="embed">
                <EmbedRenderer
                    :data="embedData"
                    :options="embedOptions"
                >
                    <slot />
                </EmbedRenderer>
            </component>
        `,
        components: {
            EmbedRenderer,
        },
        inject: [
            'state',
        ],
        props: {
            ...embedOptions.attributes
                ?.filter(name => name !== 'slot')
                .reduce((res, attributeName) => ({
                    ...res,
                    [attributeName]: null,
                }), {}),
        },
        data() {
            return {
                index: 0,
            }
        },
        computed: {
            embedOptions() {
                return embedOptions;
            },
            embedData() {
                const additionalData = this.state.embeds[embedOptions.key][this.index];

                return {
                    ...embedOptions.attributes?.reduce((res, attributeName) => ({
                        ...res,
                        [attributeName]: parseAttributeValue(this.$props[attributeName]),
                    }), {}),
                    ...additionalData,
                }
            },
        },
        created() {
            this.index = this.state.embeds[embedOptions.key].length;
            this.state.embeds[embedOptions.key].push(this.embedData);

            ignoreVueElement(this.embedOptions.tag);
        },
    }
}
