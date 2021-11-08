import { mount } from '@cypress/vue';
import EditorField from "sharp-form/src/components/fields/editor/EditorField.vue";

function mountEditor(options) {
  mount({
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
      ...options?.propsData,
    },
    provide: {
      $form: {
        locales: [],
      }
    },
  });
}

describe('editor', () => {
  it('mount', () => {
    mountEditor({
      propsData: {
        value: { text:'<strong>test</strong>' },
      }
    });
    cy.contains('strong', 'test')
      .get('[contenteditable]').type('1')
      .get('input[name="test_value"]').invoke('val').should('equal', '<p><strong>test1</strong></p>')
  });
  it('mount markdown', () => {
    mountEditor({
      propsData: {
        value: { text:'**test**' },
        markdown: true,
      },
    });
    cy.contains('strong', 'test')
      .get('[contenteditable]').type('1')
      .get('input[name="test_value"]').invoke('val').should('equal', '**test1**')
  });
})

