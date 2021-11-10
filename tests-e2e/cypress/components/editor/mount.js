import { mount } from "@cypress/vue";
import EditorField from "sharp-form/src/components/fields/editor/EditorField";


export function mountEditor(options) {
  mount({
    inheritAttrs: false,
    template: `
      <EditorField
        :value="value" 
        v-bind="$attrs"
        @input="value = $event" 
      />
    `,
    components: {
      EditorField
    },
    data: () => ({
      value: options?.propsData?.value,
    }),
  }, {
    ...options,
    propsData: {
      toolbar: [
        "bold", "italic", "link", "|",
        "heading-1", "heading-2", "heading-3", "horizontal-rule", "|",
        "ordered-list", "bullet-list", "blockquote", "code", "upload-image", "|",
        "table", "iframe", "html", "|",
        "highlight"
      ],
      uniqueIdentifier: 'test_value',
      innerComponents: {
        upload: {}
      },
      ...options?.propsData,
    },
    provide: {
      $form: {
        locales: [],
      }
    },
  });
}
