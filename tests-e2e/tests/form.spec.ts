import { test, expect } from '@playwright/test';
import { init } from "../helpers";


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
      await menu.getByRole('combobox').fill('2');
      await expect(menu.getByRole('option')).toHaveCount(1);
      await menu.getByRole('option', { name: 'Option 2' }).click();
      await expect(page.getByRole('combobox', { name: 'Autocomplete local' })).toContainText('Option 2');
      await page.getByRole('combobox', { name: 'Autocomplete local' }).click();
      await menu.getByRole('combobox').clear();
      await expect(menu.getByRole('option')).toHaveCount(10);
      await menu.getByRole('option', { name: 'Option 3' }).click();
      await expect(page.getByRole('combobox', { name: 'Autocomplete local' })).toContainText('Option 3');
      await expect(menu).not.toBeVisible();
      await page.getByRole('group', { name: 'Autocomplete local' }).getByRole('button', { name: 'Clear' }).click();
      await expect(page.getByRole('combobox', { name: 'Autocomplete local' })).toContainText('Search...');
      await page.getByRole('combobox', { name: 'Autocomplete local' }).click();
      await menu.getByRole('combobox').fill('foobar');
      await expect(menu).toContainText('No results found');
    });
    test('autocomplete remote', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      await page.getByRole('combobox', { name: 'Autocomplete remote' }).click();
      const menu = page.getByRole('dialog');
    });
  });
});




