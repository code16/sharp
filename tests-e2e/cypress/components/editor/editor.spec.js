import { mountEditor } from "./mount";
import { editorField, editorValue, toolbarButton } from "./util";


it('mount', () => {
  mountEditor({
    propsData: {
      value: { text:'<strong>test</strong>' },
    }
  });
  editorField()
    .contains('strong', 'test')
    .type('1')
  editorValue().should('equal', '<p><strong>test1</strong></p>')
});

it('mount markdown', () => {
  mountEditor({
    propsData: {
      value: { text:'**test**' },
      markdown: true,
    },
  });
  editorField()
    .contains('strong', 'test')
    .type('1')
  editorValue().should('equal', '**test1**')
});

describe('toolbar', () => {
  function testMark(buttonName, tag) {
    return () => {
      mountEditor({
        propsData: {
          value: { text:'test' },
        },
      });
      editorField().type('{selectall}')
      toolbarButton(buttonName).click()
      editorField().contains(tag.replace(/<>/, ''), 'test')
    }
  }

  function testNode(buttonName, tag, text = 'test') {
    return () => {
      mountEditor({
        propsData: {
          value: { text },
        },
      });
      editorField().type('{selectall}')
      toolbarButton(buttonName).click()
      editorField().contains(tag.replace(/<>/, ''), text || /^$/)
    }
  }

  it('bold', testMark('bold', '<strong>'));
  it('italic', testMark('italic', '<em>'));
  it('highlight', testMark('highlight', '<mark>'));

  it.skip('link', () => {
    mountEditor();
    toolbarButton('link').click()
  });

  it('heading-1', testNode('heading-1', '<h1>'));
  it('heading-2', testNode('heading-2', '<h2>'));
  it('heading-3', testNode('heading-3', '<h3>'));
  it('horizontal-rule', testNode('horizontal-rule', '<hr>', null));
  it('bullet-list', testNode('bullet-list', '<ul>'));
  it('ordered-list', testNode('ordered-list', '<ol>'));
  it('blockquote', testNode('blockquote', '<blockquote>'));

  it.skip('table', () => {
    mountEditor();
    toolbarButton('table').click()
  });
});

