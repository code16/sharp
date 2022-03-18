import { parseAttributeValue } from "sharp-embeds";
import EmbedRenderer from "./EmbedRenderer";

export function createEmbedComponent(embedOptions) {
    return {
        name: `Embed_${embedOptions.tag}`,
        template: `
            <EmbedRenderer 
                :embed-data="embedData" 
                :embed-options="embedOptions" 
            />
        `,
        components: {
            EmbedRenderer,
        },
        inject: [
            'state',
        ],
        props: {
            ...embedOptions.attributes?.reduce((res, attributeName) => ({
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
        },
    }
}
