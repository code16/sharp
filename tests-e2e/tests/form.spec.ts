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
    test('date time', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      await page.getByLabel('Date time').fill('2021-01-01T12:34');
      await createForm(page);
      await expect(page.getByLabel('Date time')).toHaveValue('2021-01-01T12:34');
    });
  });
});




