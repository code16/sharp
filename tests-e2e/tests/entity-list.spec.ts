// noinspection DuplicatedCode

import { test, test as base, expect, TestType } from '@playwright/test';
import { init, InitOptions } from "../helpers";
import { today, getLocalTimeZone } from "@internationalized/date";
import {
  PlaywrightTestArgs,
  PlaywrightTestOptions,
  PlaywrightWorkerArgs,
  PlaywrightWorkerOptions
} from "playwright/types/test";
import { commandFormResultSuite, commandSuite, CommandSuiteArgs } from "./commands";

type EntityListSuiteArgs = {
  init: (options?: InitOptions) => Promise<void>,
  goto: () => Promise<void>,
  reload: () => Promise<void>
}

test.describe('entity list', () => {
  test('display entity list', async ({ page }) => {
    await init(page, { seed: { entityList: true } });
    await page.goto('/sharp/s-list/test-models');
    await expect(page.getByRole('heading', { name: 'Test models' })).toBeVisible();
    await expect(page.getByRole('table')).toBeVisible();
    await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
  });
  entityListSuite(base.extend<EntityListSuiteArgs>({
    init: ({ page }, use) => use(async (options) => {
      await init(page, { seed: { entityList: true }, ...options });
    }),
    goto: ({ page }, use) => use(async () => {
      await page.goto('/sharp/s-list/test-models');
    }),
    reload: ({ page }, use) => use(async () => {
      await page.reload();
    }),
  }));
  test.describe('commands', () => {
    test.describe('entity', () => {
      commandSuite(base.extend<CommandSuiteArgs>({
        init: ({ page }, use) => use(async () => {
          await init(page, { seed: { entityList: true } });
        }),
        goto: ({ page }, use) => use(async () => {
          await page.goto('/sharp/s-list/test-models');
        }),
        openCommandDropdown: ({ page }, use) => use(async () => {
          await page.getByRole('button', { name: 'Actions' }).first().click();
        }),
        contentLocator: ({ page }, use) => use(page.getByRole('table')),
      }));
      test.describe('form result', () => {
        commandFormResultSuite(base.extend<CommandSuiteArgs>({
          init: ({ page }, use) => use(async () => {
            await init(page, { seed: { entityList: true } });
          }),
          goto: ({ page }, use) => use(async () => {
            await page.goto('/sharp/s-list/test-models');
          }),
          openCommandDropdown: ({ page }, use) => use(async () => {
            await page.getByRole('button', { name: 'Actions' }).first().click();
          }),
          contentLocator: ({ page }, use) => use(page.getByRole('table')),
        }));
      });
    });
    test.describe('instance', () => {
      commandSuite(base.extend<CommandSuiteArgs>({
        init: ({ page }, use) => use(async () => {
          await init(page, { seed: { entityList: true } });
        }),
        goto: ({ page }, use) => use(async () => {
          await page.goto('/sharp/s-list/test-models');
        }),
        openCommandDropdown: ({ page }, use) => use(async () => {
          await page.getByRole('table').getByRole('button', { name: 'Actions' }).first().click();
        }),
        contentLocator: ({ page }, use) => use(page.getByRole('table')),
      }));
    });
  });
});

test.describe('show entity list', () => {
  entityListSuite(base.extend<EntityListSuiteArgs>({
    init: ({ page }, use) => use(async (options) => {
      await init(page, { seed: { entityList: true }, ...options });
    }),
    goto: ({ page }, use) => use(async () => {
      await page.goto('/sharp/s-list/test-models/s-show/test-models/1');
    }),
    reload: ({ page }, use) => use(async () => {
    }),
  }));
  test.describe('commands', () => {
    test.describe('entity', () => {
      commandSuite(base.extend<CommandSuiteArgs>({
        init: ({ page }, use) => use(async () => {
          await init(page, { seed: { entityList: true } });
        }),
        goto: ({ page }, use) => use(async () => {
          await page.goto('/sharp/s-list/test-models/s-show/test-models/1');
        }),
        openCommandDropdown: ({ page }, use) => use(async () => {
          await page.getByRole('region', { name: 'Test models' }).getByRole('button', { name: 'Actions' }).first().click();
        }),
        contentLocator: ({ page }, use) => use(page.getByRole('region', { name: 'Test models' }).getByRole('table')),
      }));
    });
    test.describe('instance', () => {
      commandSuite(base.extend<CommandSuiteArgs>({
        init: ({ page }, use) => use(async () => {
          await init(page, { seed: { entityList: true } });
        }),
        goto: ({ page }, use) => use(async () => {
          await page.goto('/sharp/s-list/test-models/s-show/test-models/1');
        }),
        openCommandDropdown: ({ page }, use) => use(async () => {
          await page.getByRole('region', { name: 'Test models' })
            .getByRole('table')
            .getByRole('button', { name: 'Actions' })
            .first()
            .click();
        }),
        contentLocator: ({ page }, use) => use(page.getByRole('region', { name: 'Test models' }).getByRole('table')),
      }));
    });
  });
});

function entityListSuite(test: TestType<PlaywrightTestArgs & PlaywrightTestOptions & EntityListSuiteArgs, PlaywrightWorkerArgs & PlaywrightWorkerOptions>) {
  test.describe('filters', () => {
    test('check', async ({ page, init, goto, reload }) => {
      await init();
      await goto();
      await expect(page.getByRole('checkbox', { name: 'Check' })).not.toBeChecked();
      await page.getByRole('checkbox', { name: 'Check' }).click();
      await expect(page.getByRole('checkbox', { name: 'Check' })).toBeChecked();
      await expect(page.getByText('1 item', { exact: true }).first()).toBeVisible();
      await reload();
      await expect(page.getByRole('checkbox', { name: 'Check' })).toBeChecked();
      await expect(page.getByText('1 item', { exact: true }).first()).toBeVisible();
      await page.getByRole('checkbox', { name: 'Check' }).click();
      await expect(page.getByRole('checkbox', { name: 'Check' })).not.toBeChecked();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
      await page.getByRole('checkbox', { name: 'Check' }).click();
      await expect(page.getByRole('checkbox', { name: 'Check' })).toBeChecked();
      await expect(page.getByText('1 item', { exact: true }).first()).toBeVisible();
      await page.getByRole('button', { name: 'Reset all' }).click();
      await expect(page.getByRole('checkbox', { name: 'Check' })).not.toBeChecked();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
    });
    test('date range', async ({ page, init, goto, reload }) => {
      await init();
      await goto();
      await page.getByRole('combobox', { name: 'Date Range', exact: true }).click();
      await page.getByRole('dialog').getByRole('button', { name: '10' }).first().click();
      await page.getByRole('dialog').getByRole('button', { name: '10' }).last().click();
      await page.getByRole('dialog').getByRole('button', { name: 'Update' }).click();
      await expect(page.getByText('0 item', { exact: true }).first()).toBeVisible();
      const startDate = today(getLocalTimeZone()).set({ day: 10 });
      const endDate = today(getLocalTimeZone()).set({ day: 10 }).add({ months: 1 });
      await expect(page.getByText(`${startDate.toString()} - ${endDate.toString()}`, { exact: true })).toBeVisible();
      await reload();
      await expect(page.getByText(`${startDate.toString()} - ${endDate.toString()}`, { exact: true })).toBeVisible();
      await page.getByRole('combobox', { name: 'Date Range', exact: true }).click();
      await page.getByRole('dialog').getByRole('button', { name: 'Reset' }).click();
      await expect(page.getByText(`${startDate.toString()} - ${endDate.toString()}`, { exact: true })).not.toBeVisible();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
      await reload();
      await expect(page.getByText(`${startDate.toString()} - ${endDate.toString()}`, { exact: true })).not.toBeVisible();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
      await page.getByRole('combobox', { name: 'Date Range', exact: true }).click();
      await page.getByRole('dialog').getByRole('textbox', { name: 'Start date' }).fill('2022-01-01');
      await page.getByRole('dialog').getByRole('textbox', { name: 'End date' }).fill('2022-01-30');
      await page.getByRole('dialog').getByRole('button', { name: 'Update' }).click();
      await expect(page.getByText('0 item', { exact: true }).first()).toBeVisible();
      await expect(page.getByText(`2022-01-01 - 2022-01-30`, { exact: true })).toBeVisible();
      await reload();
      await expect(page.getByText('0 item', { exact: true }).first()).toBeVisible();
      await expect(page.getByText(`2022-01-01 - 2022-01-30`, { exact: true })).toBeVisible();
      await page.getByRole('button', { name: 'Reset all' }).click();
      await expect(page.getByText(`2022-01-01 - 2022-01-30`, { exact: true })).not.toBeVisible();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
    });
    test('date range required', async ({ page, init, goto, reload }) => {
      await init();
      await goto();
      async function valuate() {
        await test.step('valuate', async () => {
          await page.getByRole('combobox', { name: 'Date Range required', exact: true }).click();
          await page.getByRole('dialog').getByRole('textbox', { name: 'Start date' }).fill('2021-03-01');
          await page.getByRole('dialog').getByRole('textbox', { name: 'End date' }).fill('2021-03-30');
          await page.getByRole('dialog').getByRole('button', { name: 'Update' }).click();
          await expect(page.getByText(`2021-03-01 - 2021-03-30`, { exact: true })).toBeVisible();
        })
      }
      await expect(page.getByText('2021-01-01 - 2021-01-31',{ exact: true })).toBeVisible();
      await valuate();
      await page.getByRole('combobox', { name: 'Date Range required', exact: true }).click();
      await page.getByRole('dialog').getByRole('button', { name: 'Reset' }).click();
      await expect(page.getByText('2021-01-01 - 2021-01-31', { exact: true })).toBeVisible();
      await valuate();
      await page.getByRole('button', { name: 'Reset all' }).click();
      await expect(page.getByText('2021-01-01 - 2021-01-31', { exact: true })).toBeVisible();
    });
    test('select', async ({ page, init, goto, reload }) => {
      await init();
      await goto();
      async function valuate() {
        await test.step('valuate', async () => {
          await page.getByRole('combobox', { name: 'Select', exact: true }).click();
          await page.getByRole('listbox').getByRole('option', { name: 'Option 1', exact: true }).click();
          await expect(page.getByRole('listbox')).toBeHidden();
          await expect(page.getByText('Option 1', { exact: true }).first()).toBeVisible();
          await expect(page.getByText('1 item', { exact: true }).first()).toBeVisible();
        });
      }
      await valuate();
      await reload();
      await expect(page.getByText('Option 1', { exact: true }).first()).toBeVisible();
      await expect(page.getByText('1 item', { exact: true }).first()).toBeVisible();
      await page.getByRole('combobox', { name: 'Select', exact: true }).click();
      await page.getByRole('listbox').getByRole('option', { name: 'Reset', exact: true }).click();
      await expect(page.getByRole('listbox')).toBeHidden();
      await expect(page.getByText('Option 1', { exact: true }).first()).not.toBeVisible();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
      await valuate();
      await page.getByRole('button', { name: 'Reset all' }).click();
      await expect(page.getByText('Option 1', { exact: true }).first()).not.toBeVisible();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
    });
    test('select multiple', async ({ page, init, goto, reload }) => {
      await init();
      await goto();
      async function valuate() {
        await test.step('valuate', async () => {
          await page.getByRole('combobox', { name: 'Select multiple', exact: true }).click();
          await page.getByRole('listbox').getByRole('option', { name: 'Option 1', exact: true }).click();
          await expect(page.getByRole('listbox').getByRole('option', { name: 'Option 1', exact: true }).getByRole('checkbox')).toBeChecked();
          await page.getByRole('listbox').getByRole('option', { name: 'Option 2', exact: true }).click();
          await expect(page.getByRole('listbox').getByRole('option', { name: 'Option 2', exact: true }).getByRole('checkbox')).toBeChecked();
          await page.mouse.click(0, 0);
          await expect(page.getByRole('listbox')).toBeHidden();
          await expect(page.getByText('Option 1', { exact: true }).first()).toBeVisible();
          await expect(page.getByText('Option 2', { exact: true }).first()).toBeVisible();
          await expect(page.getByText('2 items', { exact: true }).first()).toBeVisible();
        });
      }
      await valuate();
      await reload();
      await expect(page.getByText('Option 1', { exact: true }).first()).toBeVisible();
      await expect(page.getByText('Option 2', { exact: true }).first()).toBeVisible();
      await expect(page.getByText('2 items', { exact: true }).first()).toBeVisible();
      await page.getByRole('combobox', { name: 'Select multiple', exact: true }).click();
      await page.getByRole('listbox').getByRole('option', { name: 'Reset', exact: true }).click();
      await expect(page.getByText('Option 1', { exact: true }).first()).not.toBeVisible();
      await expect(page.getByText('Option 2', { exact: true }).first()).not.toBeVisible();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
      await valuate();
      await page.getByRole('button', { name: 'Reset all' }).click();
      await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
    });
    test('select required', async ({ page, init, goto, reload }) => {
      await init();
      await goto();
      async function valuate() {
        await test.step('valuate', async () => {
          await page.getByRole('combobox', { name: 'Select required', exact: true }).click();
          await page.getByRole('listbox').getByRole('option', { name: 'Option 3', exact: true }).click();
          await expect(page.getByRole('listbox')).toBeHidden();
          await expect(page.getByText('Option 3', { exact: true }).first()).toBeVisible();
        });
      }
      await expect(page.getByText('Option 7', { exact: true }).first()).toBeVisible();
      await valuate();
      await reload();
      await expect(page.getByText('Option 3', { exact: true }).first()).toBeVisible();
      await page.getByRole('combobox', { name: 'Select required', exact: true }).click();
      await page.getByRole('listbox').getByRole('option', { name: 'Reset', exact: true }).click();
      await expect(page.getByRole('listbox')).toBeHidden();
      await valuate();
      await page.getByRole('button', { name: 'Reset all' }).click();
      await expect(page.getByText('Option 7', { exact: true }).first()).toBeVisible();
    });
  });
  test('search', async ({ page, init, goto, reload }) => {
    await init();
    await goto();
    await page.getByRole('searchbox', { name: 'Search...' }).fill('search');
    await page.getByRole('button', { name: 'Search' }).click();
    await expect(page.getByText('1 result for “search”', { exact: true }).first()).toBeVisible();
    await expect(page.getByRole('row').filter({ has: page.getByRole('cell') })).toHaveCount(1);
    await reload();
    await expect(page.getByRole('searchbox', { name: 'Search...' })).toHaveValue('search');
    await expect(page.getByText('1 result for “search”', { exact: true }).first()).toBeVisible();
    await expect(page.getByRole('row').filter({ has: page.getByRole('cell') })).toHaveCount(1);
    await page.getByRole('button', { name: 'Clear search' }).click();
    await expect(page.getByRole('searchbox', { name: 'Search...' })).toHaveValue('');
    await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
    await expect(page.getByRole('button', { name: 'Clear search' })).toBeHidden();
    await reload();
    await expect(page.getByRole('searchbox', { name: 'Search...' })).toHaveValue('');
    await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
    await page.getByRole('searchbox', { name: 'Search...' }).fill('search');
    await page.getByRole('searchbox', { name: 'Search...' }).press('Enter');
    await expect(page.getByText('1 result for “search”', { exact: true }).first()).toBeVisible();
    await page.getByRole('button', { name: 'Reset all' }).click();
    await expect(page.getByRole('searchbox', { name: 'Search...' })).toHaveValue('');
    await expect(page.getByText('20 items', { exact: true }).first()).toBeVisible();
  });
  test('pagination', async ({ page, init, goto, reload }) => {
    let tableTextContent = '';
    await init();
    await goto();
    await expect(page.getByRole('combobox', { name: 'Current page' })).toHaveText('1');
    await expect(page.getByRole('button', { name: 'Go to first page' })).toBeDisabled();
    await expect(page.getByRole('button', { name: 'Previous page' })).toBeDisabled();
    tableTextContent = await page.getByRole('table').textContent();
    await page.getByRole('link', { name: 'Next page' }).click();
    await expect(page.getByRole('combobox', { name: 'Current page' })).toHaveText('2');
    await expect(page.getByRole('table')).not.toHaveText(tableTextContent);
    tableTextContent = await page.getByRole('table').textContent();
    await reload();
    await expect(page.getByRole('table')).toHaveText(tableTextContent);
    await expect(page.getByRole('combobox', { name: 'Current page' })).toHaveText('2');
    await page.getByRole('link', { name: 'Go to last page' }).click();
    await expect(page.getByRole('combobox', { name: 'Current page' })).toHaveText('4');
    await page.getByRole('link', { name: 'Previous page' }).click();
    await expect(page.getByRole('combobox', { name: 'Current page' })).toHaveText('3');
    await page.getByRole('link', { name: 'Go to first page' }).click();
    await expect(page.getByRole('combobox', { name: 'Current page' })).toHaveText('1');
    tableTextContent = await page.getByRole('table').textContent();
    await page.getByRole('combobox', { name: 'Current page' }).click();
    await page.getByRole('listbox').getByRole('option', { name: '3' }).click();
    await expect(page.getByRole('combobox', { name: 'Current page' })).toHaveText('3');
    await expect(page.getByRole('table')).not.toHaveText(tableTextContent);
  });
  test('sort', async ({ page, init, goto, reload }) => {
    let tableTextContent = '';
    await init({
      session: {
        default_sort: 'id',
      }
    });
    await goto();
    tableTextContent = await page.getByRole('table').textContent();
    await page.getByRole('button', { name: 'Id: Sort descending' }).click();
    await expect(page.getByRole('table')).not.toHaveText(tableTextContent);
    tableTextContent = await page.getByRole('table').textContent();
    await reload();
    await expect(page.getByRole('table')).toHaveText(tableTextContent);
    await page.getByRole('button', { name: 'Id: Sort ascending' }).click();
    await expect(page.getByRole('table')).not.toHaveText(tableTextContent);
    tableTextContent = await page.getByRole('table').textContent();
    await page.getByRole('button', { name: 'Text: Sort ascending' }).click();
    await expect(page.getByRole('table')).not.toHaveText(tableTextContent);
    tableTextContent = await page.getByRole('table').textContent();
    await reload();
    await expect(page.getByRole('table')).toHaveText(tableTextContent);
    await page.getByRole('button', { name: 'Text: Sort descending' }).click();
    await page.getByRole('button', { name: 'Reset to default sort' }).click();
    await page.getByRole('button', { name: 'Id: Sort descending' }).click();
  });
  test('delete', async ({ page, init, goto, reload }) => {
    let tableTextContent = '';
    await init();
    await goto();
    tableTextContent = await page.getByRole('table').textContent();
    await page.getByRole('table').getByRole('button', { name: 'Actions' }).first().click();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Delete...' }).click();
    await page.getByRole('alertdialog').getByRole('button', { name: 'Delete' }).click();
    await expect(page.getByText('19 items', { exact: true }).first()).toBeVisible();
    await expect(page.getByRole('table')).not.toHaveText(tableTextContent);
  });
  test('selection', async ({ page, init, goto, reload }) => {
    await init();
    await goto();
    await page.getByRole('button', { name: 'Select...' }).click();
    await page.getByRole('checkbox', { name: 'Select row' }).first().click();
    await page.getByRole('button', { name: 'Actions (1 selected)' }).first().click();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test selection command' }).click();
    await expect(page.getByText('Selection changed')).toHaveCount(1);
    await page.getByRole('button', { name: 'Select...' }).click();
    await page.getByRole('checkbox', { name: 'Select all in current page' }).click();
    for(const checkbox of await page.getByRole('checkbox', { name: 'Select row' }).all()) {
      await expect(checkbox).toBeChecked();
    }
    await page.getByRole('button', { name: 'Actions (5 selected)' }).first().click();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test selection command' }).click();
    await expect(page.getByText('Selection changed')).toHaveCount(5);
  });
  test('reorder', async ({ page, init, goto, reload }) => {
    await init();
    await goto();
    const tbody = page.getByRole('rowgroup').nth(1);
    await page.getByRole('button', { name: 'Reorder' }).click();
    await expect(page.getByRole('table').getByRole('button', { name: 'Actions' }).first()).toBeHidden();
    const initialTextContent = await tbody.textContent();
    await tbody.getByRole('row').nth(0).dragTo(tbody.getByRole('row').nth(1));
    await expect(tbody).not.toHaveText(initialTextContent);
    await page.getByRole('button', { name: 'Cancel' }).click();
    await expect(tbody).toHaveText(initialTextContent);
    await page.getByRole('button', { name: 'Reorder' }).click();
    await tbody.getByRole('row').nth(0).dragTo(tbody.getByRole('row').nth(1));
    await expect(tbody).not.toHaveText(initialTextContent);
    const reorderedTextContent = await tbody.textContent();
    await page.getByRole('button', { name: 'Finish' }).click();
    await expect(page.getByRole('button', { name: 'Finish' })).toBeHidden();
    await expect(tbody).not.toHaveText(initialTextContent);
    await expect(tbody).toHaveText(reorderedTextContent);
    await page.reload();
    await expect(tbody).not.toHaveText(initialTextContent);
    await expect(tbody).toHaveText(reorderedTextContent);
  });
  test.describe('quick creation form', () => {
    test('create new', async ({ page, init, goto, reload }) => {
      await init({
        seed: {
          show: true,
          entityList: false,
        },
        session: {
          quick_creation_form: '1',
        },
      });
      await goto();
      await page.getByRole('link', { name: 'New...' }).click();
      await page.getByRole('dialog').getByRole('textbox', { name: 'Text', exact: true }).fill('quick created');
      await page.getByRole('dialog').getByRole('button', { name: 'Create', exact: true }).click();
      await expect(page.getByRole('dialog')).toBeHidden();
      await expect(page.getByText('quick created', { exact: true })).toBeVisible();
    });
    test('validation', async ({ page, init, goto, reload }) => {
      await init({
        seed: {
          show: true,
          entityList: false,
        },
        session: {
          quick_creation_form: '1',
          entity_list_multiform: '1',
        },
      });
      await goto();
      await page.getByRole('button', { name: 'New' }).click();
      await page.getByRole('menu').getByRole('menuitem', { name: 'required' }).click();
      await page.getByRole('dialog').getByRole('button', { name: 'Create', exact: true }).click();
      await expect(page.getByRole('dialog')).toBeVisible();
      await expect(page.getByText('required.').first()).toBeVisible();
    });
    test('create new, redirect to show page', async ({ page, init, goto, reload }) => {
      await init({
        seed: {
          show: true,
          entityList: false,
        },
        session: {
          quick_creation_form: '1',
          display_show_page_after_creation: '1',
        },
      });
      await goto();
      await page.getByRole('link', { name: 'New...' }).click();
      await page.getByRole('dialog').getByRole('textbox', { name: 'Text', exact: true }).fill('quick created');
      await page.getByRole('dialog').getByRole('button', { name: 'Create', exact: true }).click();
      await expect(page.getByRole('dialog')).toBeHidden();
      await expect(page.getByRole('heading', { name: 'quick created' })).toBeVisible();
    });
  });
}


