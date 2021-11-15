

function logCalling(functionName, ...args) {
  return cy.then(() => Cypress.log({ name:`${functionName}()`, message: args.join(', ') }));
}


export function editorField() {
  return logCalling('editorField')
    .get('[contenteditable]', { log: false });
}

export function editorValue() {
  return logCalling('editorValue')
    .get('input[name="test_value"]', { log: false })
    .invoke({ log: false }, 'val');
}

export function toolbarButton(buttonName) {
  return logCalling('toolbarButton', buttonName)
    .get(`[data-test="${buttonName}"]`)
}

