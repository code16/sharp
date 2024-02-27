import { parseAttributeValue } from "@/embeds/utils/attributes";
import EmbedRenderer from '@/embeds/components/EmbedRenderer.vue';
import { EmbedData } from "@/types";

export function createEmbedComponent(embed: EmbedData) {
    return {
        name: `Embed_${embed.tag}`,
        template: `
            <component :is="embed.tag" class="embed">
                <EmbedRenderer
                    :data="embedData"
                    :embed="embed"
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
            ...embed.attributes
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
            embed() {
                return embed;
            },
            embedData() {
                const additionalData = this.state.embeds[embed.key][this.index];

                return {
                    ...embed.attributes?.reduce((res, attributeName) => ({
                        ...res,
                        [attributeName]: parseAttributeValue(this.$props[attributeName]),
                    }), {}),
                    ...additionalData,
                }
            },
        },
        created() {
            this.index = this.state.embeds[embed.key].length;
            this.state.embeds[embed.key].push(this.embedData);

            // ignoreVueElement(this.embedOptions.tag);
        },
    }
}
