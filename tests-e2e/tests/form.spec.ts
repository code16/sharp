// noinspection DuplicatedCode

import { test, expect, Page } from '@playwright/test';
import { init } from "../helpers";


async function createForm(page: Page) {
  await test.step('createForm', async () => {
    const responsePromise = page.waitForResponse('**/s-form/test-models');
    await page.getByRole('button', { name: 'Create' }).click();
    await responsePromise;
    await page.goto('/sharp/s-list/test-models/s-form/test-models/1');
  });
}

test.describe('form', () => {
  test('display create form', async ({ page }) => {
    await init(page);
    await page.goto('/sharp');
    await page.getByRole('link', { name: 'New...' }).click();
    await expect(page.getByRole('heading', { name: 'New “Test model”' })).toBeVisible();
  });
  test.describe('fields', () => {
    test('autocomplete local', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      await page.getByRole('combobox', { name: 'Autocomplete local' }).click();
      const menu = page.getByRole('dialog');
      await expect(menu.getByRole('option')).toHaveCount(10);
      await menu.getByRole('combobox').fill('foobar');
      await expect(menu.getByRole('option')).toHaveCount(0);
      await expect(menu).toContainText('No results found');
      await menu.getByRole('combobox').fill('2');
      await expect(menu.getByRole('option')).toHaveCount(1);
      await menu.getByRole('option', { name: 'Option 2' }).click();
      await expect(menu).not.toBeVisible();
      await expect(page.getByRole('combobox', { name: 'Autocomplete local' })).toContainText('Option 2');
      await page.getByRole('combobox', { name: 'Autocomplete local' }).click();
      await menu.getByRole('combobox').clear();
      await expect(menu.getByRole('option')).toHaveCount(10);
      await page.mouse.click(0, 0);
      await expect(menu).not.toBeVisible();
      await page.getByRole('group', { name: 'Autocomplete local' }).getByRole('button', { name: 'Clear' }).click();
      await expect(page.getByRole('combobox', { name: 'Autocomplete local' })).toContainText('Search...');
      await page.getByRole('combobox', { name: 'Autocomplete local' }).click();
      await menu.getByRole('option', { name: 'Option 2' }).click();
      await createForm(page);
      await expect(page.getByRole('combobox', { name: 'Autocomplete local' })).toContainText('Option 2');

    });
    test('autocomplete remote endpoint', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      await page.getByRole('combobox', { name: 'Autocomplete endpoint remote' }).click();
      const menu = page.getByRole('dialog');
      await expect(menu).toContainText('Enter at least 1 character to search');
      await menu.getByRole('combobox').fill('foobar');
      await expect(menu).toContainText('No results found');
      await menu.getByRole('combobox').fill('2');
      await expect(menu.getByRole('option')).toHaveCount(1);
      await menu.getByRole('option', { name: 'Option 2' }).click();
      await expect(menu).not.toBeVisible();
      await expect(page.getByRole('combobox', { name: 'Autocomplete endpoint remote' })).toContainText('Option 2');
      await page.getByRole('group', { name: 'Autocomplete endpoint remote' }).getByRole('button', { name: 'Clear' }).click();
      await expect(page.getByRole('combobox', { name: 'Autocomplete endpoint remote' })).toContainText('Search...');
      await page.getByRole('combobox', { name: 'Autocomplete endpoint remote' }).click();
      await menu.getByRole('combobox').fill('2');
      await menu.getByRole('option', { name: 'Option 2' }).click();
      await createForm(page);
      await expect(page.getByRole('combobox', { name: 'Autocomplete endpoint remote' })).toContainText('Option 2');
    });
    test('autocomplete remote callback', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      await page.getByRole('combobox', { name: 'Autocomplete callback remote' }).click();
      const menu = page.getByRole('dialog');
      await expect(menu.getByRole('option')).toHaveCount(10);
      await menu.getByRole('combobox').fill('foobar');
      await expect(menu).toContainText('No results found');
      await menu.getByRole('combobox').fill('2');
      await expect(menu.getByRole('option')).toHaveCount(1);
      await menu.getByRole('option', { name: 'Option 2' }).click();
      await expect(menu).not.toBeVisible();
      await expect(page.getByRole('combobox', { name: 'Autocomplete callback remote' })).toContainText('Option 2');
      await page.getByRole('group', { name: 'Autocomplete callback remote' }).getByRole('button', { name: 'Clear' }).click();
      await expect(page.getByRole('combobox', { name: 'Autocomplete callback remote' })).toContainText('Search...');
      await page.getByRole('combobox', { name: 'Autocomplete callback remote' }).click();
      await menu.getByRole('combobox').clear();
      await expect(menu.getByRole('option')).toHaveCount(10);
      await menu.getByRole('option', { name: 'Option 2' }).click();
      await createForm(page);
      await expect(page.getByRole('combobox', { name: 'Autocomplete remote callback' })).toContainText('Option 2');
    });
    test('autocomplete list', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const list = page.getByLabel('Autocomplete list');
      await list.getByRole('button', { name: 'Add an item' }).click();
      await list.getByRole('combobox', { name: 'Autocomplete list item' }).click();
      const menu = page.getByRole('dialog');
      await expect(menu.getByRole('option')).toHaveCount(10);
      await menu.getByRole('option', { name: 'Option 2' }).click();
      await list.getByRole('button', { name: 'Actions' }).click();
      await page.getByRole('menu').getByRole('menuitem', { name: 'Remove' }).click();
      await list.getByRole('button', { name: 'Add an item' }).click();
      await list.getByRole('combobox', { name: 'Autocomplete list item' }).click();
      await menu.getByRole('option', { name: 'Option 3' }).click();
      await list.getByRole('button', { name: 'Add an item' }).click();
      await list.getByRole('combobox', { name: 'Autocomplete list item' }).last().click();
      await menu.getByRole('option', { name: 'Option 4' }).click();
      await createForm(page);
      await expect(list.getByRole('combobox', { name: 'Autocomplete list item' }).first()).toContainText('Option 3');
      await expect(list.getByRole('combobox', { name: 'Autocomplete list item' }).last()).toContainText('Option 4');
    });
    test('check', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      await page.getByRole('checkbox', { name: 'Check' }).click();
      await createForm(page);
      await expect(page.getByRole('checkbox', { name: 'Check' })).toBeChecked();
    });
    test('date', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const input = page.getByLabel('Date', { exact: true });
      await input.fill('2021-01-01');
      await page.getByRole('button', { name: 'Clear Date', exact: true }).click();
      await expect(input).toHaveValue('');
      await input.fill('2021-01-01');
      await createForm(page);
      await expect(input).toHaveValue('2021-01-01');
    });
    test('date picker', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const input = page.getByLabel('Date', { exact: true });
      await input.fill('2021-01-01');
      await input.click();
      await page.getByRole('dialog').getByText('January', { exact: true }).click();
      await page.getByRole('listbox').getByText('March', { exact: true }).click();
      await page.getByRole('dialog').getByText('2021', { exact: true }).click();
      await page.getByRole('listbox').getByText('2022', { exact: true }).click();
      await page.getByRole('dialog').getByText('26').click();
      await expect(input).toHaveValue('2022-03-26');
      await page.mouse.click(0, 0);
      await createForm(page);
      await expect(input).toHaveValue('2022-03-26');
    });
    test('date time', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const input = page.getByLabel('Date time', { exact: true });
      await input.fill('2021-01-01T12:34');
      await page.getByRole('button', { name: 'Clear Date time' }).click();
      await expect(input).toHaveValue('');
      await input.fill('2021-01-01T12:34');
      await createForm(page);
      await expect(input).toHaveValue('2021-01-01T12:34');
    });
    test('date time picker', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const input = page.getByLabel('Date time', { exact: true });
      await input.fill('2021-01-01T12:34');
      await input.click();
      await page.getByRole('dialog').getByText('January', { exact: true }).click();
      await page.getByRole('listbox').getByText('March', { exact: true }).click();
      await page.getByRole('dialog').getByText('2021', { exact: true }).click();
      await page.getByRole('listbox').getByText('2022', { exact: true }).click();
      await page.getByRole('dialog').getByText('26').click();
      await page.getByRole('dialog').getByText('11').last().click();
      await page.getByRole('dialog').getByText('30').last().click();
      await expect(input).toHaveValue('2022-03-26T11:30');
      await page.mouse.click(0, 0);
      await createForm(page);
      await expect(input).toHaveValue('2022-03-26T11:30');
    });
    test('time', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const input = page.getByLabel('Time', { exact: true });
      await input.fill('12:34');
      await page.getByRole('button', { name: 'Clear Time', exact: true }).click();
      await expect(input).toHaveValue('');
      await input.fill('12:34');
      await createForm(page);
      await expect(input).toHaveValue('12:34');
    });
    test('time picker', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const input = page.getByLabel('Time', { exact: true });
      await input.fill('12:34');
      await input.click();
      await page.getByRole('dialog').getByText('11').last().click();
      await page.getByRole('dialog').getByText('30').last().click();
      await expect(input).toHaveValue('11:30');
      await page.mouse.click(0, 0);
      await createForm(page);
      await expect(input).toHaveValue('11:30');
    });
    test('geolocation', () => {
      // todo
    });
    test('html', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      expect(await page.getByLabel('Html', { exact: true }).innerHTML()).toContain('Your name is <strong>John</strong>');
    });
    test('list', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const list = page.getByLabel('List', { exact: true });
      await list.getByRole('button', { name: 'Add an item' }).click();
      await list.getByRole('textbox', { name: 'List item text' }).nth(0).fill('example 1');
      await list.getByRole('button', { name: 'Add an item' }).click();
      await list.getByRole('textbox', { name: 'List item text' }).nth(1).fill('example 2');
      await list.getByRole('button', { name: 'Add an item' }).click();
      await list.getByRole('textbox', { name: 'List item text' }).nth(2).fill('example 3');
      // remove "example 2"
      await list.getByRole('listitem').nth(1).getByRole('button', { name: 'Actions' }).click();
      await page.getByRole('menu').getByRole('menuitem', { name: 'Remove' }).click();
      // insert "example 4" above "example 1"
      await list.getByRole('listitem').nth(0).getByRole('button', { name: 'Actions' }).click();
      await page.getByRole('menu').getByRole('menuitem', { name: 'Insert above' }).click();
      await list.getByRole('textbox', { name: 'List item text' }).nth(0).fill('example 4');
      await list.getByRole('button', { name: 'Reorder' }).click();
      // move "example 1" to the top
      await list.getByRole('listitem').nth(1).dragTo(list.getByRole('listitem').nth(0), { sourcePosition: { x: 20, y: 20 }, targetPosition: { x: 20, y: 20 } });
      await expect(list.getByRole('textbox', { name: 'List item text' }).nth(0)).toHaveValue('example 1');
      await expect(list.getByRole('textbox', { name: 'List item text' }).nth(1)).toHaveValue('example 4');
      await expect(list.getByRole('textbox', { name: 'List item text' }).nth(2)).toHaveValue('example 3');
      await createForm(page);
      await expect(list.getByRole('textbox', { name: 'List item text' }).nth(0)).toHaveValue('example 1');
      await expect(list.getByRole('textbox', { name: 'List item text' }).nth(1)).toHaveValue('example 4');
      await expect(list.getByRole('textbox', { name: 'List item text' }).nth(2)).toHaveValue('example 3');
    });
    test('editor', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      const editor = page.getByLabel('Editor HTML', { exact: true });
      await test.step('default text', async () => {
        await editor.getByRole('textbox').pressSequentially('default');
        await expect(editor.getByRole('textbox')).toHaveText('default');
        await editor.getByRole('textbox').pressSequentially(' ');
      });
      await test.step('bold', async () => {
        await editor.getByRole('button', { name: 'Bold' }).click();
        await editor.getByRole('textbox').pressSequentially('strong');
        await expect(editor.getByRole('textbox').locator('strong')).toHaveText('strong');
        await editor.getByRole('button', { name: 'Bold' }).click();
        await editor.getByRole('textbox').pressSequentially(' ');
      });
      await test.step('italic', async () => {
        await editor.getByRole('button', { name: 'Italic' }).click();
        await editor.getByRole('textbox').pressSequentially('italic');
        await expect(editor.getByRole('textbox').locator('em')).toHaveText('italic');
        await editor.getByRole('button', { name: 'Italic' }).click();
        await editor.getByRole('textbox').pressSequentially(' ');
      });
      await test.step('link', async () => {
        await editor.getByRole('button', { name: 'Link' }).click();
        await page.getByRole('dialog').getByRole('textbox', { name: 'text' }).fill('link');
        await page.getByRole('dialog').getByRole('textbox', { name: 'URL' }).fill('https://example.com');
        await page.getByRole('dialog').getByRole('button', { name: 'Insert link' }).click();
        await expect(editor.getByRole('textbox').locator('a')).toHaveText('link');
        await editor.getByRole('textbox').press('Enter');
      });
      await test.step('headings', async () => {
        await editor.getByRole('button', { name: 'Heading 1' }).click();
        await expect(editor.getByRole('textbox').locator('h1')).toBeVisible();
        await editor.getByRole('textbox').pressSequentially('heading 1');
        await expect(editor.getByRole('textbox').locator('h1')).toHaveText('heading 1');
        await editor.getByRole('textbox').press('Enter');
        await editor.getByRole('button', { name: 'Heading 2' }).click();
        await expect(editor.getByRole('textbox').locator('h2')).toBeVisible();
        await editor.getByRole('textbox').pressSequentially('heading 2');
        await expect(editor.getByRole('textbox').locator('h2')).toHaveText('heading 2');
        await editor.getByRole('textbox').press('Enter');
        await editor.getByRole('button', { name: 'Heading 3' }).click();
        await expect(editor.getByRole('textbox').locator('h3')).toBeVisible();
        await editor.getByRole('textbox').pressSequentially('heading 3');
        await expect(editor.getByRole('textbox').locator('h3')).toHaveText('heading 3');
        await editor.getByRole('textbox').press('Enter');
      });
      await test.step('horizontal rule', async () => {
        await editor.getByRole('button', { name: 'Horizontal rule' }).click();
        await expect(editor.getByRole('textbox').locator('hr')).toBeVisible();
      });
      await test.step('ordered list', async () => {
        await editor.getByRole('button', { name: 'Ordered list', exact: true }).click();
        await expect(editor.getByRole('textbox').locator('ol')).toBeVisible();
        await editor.getByRole('textbox').pressSequentially('ordered list');
        await editor.getByRole('textbox').press('Enter');
        await editor.getByRole('textbox').pressSequentially('ordered list');
        await editor.getByRole('textbox').press('Enter');
        await editor.getByRole('textbox').press('Enter');
        await expect(editor.getByRole('textbox').locator('ol li').filter({ hasText: 'ordered list' })).toHaveCount(2);
      });
      await test.step('unordered list', async () => {
        await editor.getByRole('button', { name: 'Unordered list' }).click();
        await expect(editor.getByRole('textbox').locator('ul')).toBeVisible();
        await editor.getByRole('textbox').pressSequentially('unordered list');
        await editor.getByRole('textbox').press('Enter');
        await editor.getByRole('textbox').pressSequentially('unordered list');
        await editor.getByRole('textbox').press('Enter');
        await editor.getByRole('textbox').press('Enter');
        await expect(editor.getByRole('textbox').locator('ul li').filter({ hasText: 'unordered list' })).toHaveCount(2);
      });
      await test.step('blockquote', async () => {
        await editor.getByRole('button', { name: 'Quote' }).click();
        await expect(editor.getByRole('textbox').locator('blockquote')).toBeVisible();
        await editor.getByRole('textbox').pressSequentially('quote');
        await editor.getByRole('textbox').press('Enter');
        await editor.getByRole('textbox').press('Enter');
        await expect(editor.getByRole('textbox').locator('blockquote')).toHaveText('quote');
      });
      await page.waitForTimeout(1000);
    });
  });
});




