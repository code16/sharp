import { test, expect } from '@playwright/test';
import { init, login, seed } from "../helpers";


test.describe('form', () => {
  test('display create form', async ({ page }) => {
    await init(page);
    await page.goto('/sharp');
    await page.getByRole('link', { name: 'New...' }).click();
    await expect(page.getByRole('heading', { name: 'New “Test model”' })).toBeVisible();
  });
  test.describe('fields', () => {
    test('autocomplete', async ({ page }) => {
      await init(page);
      await page.goto('/sharp/s-list/test-models/s-form/test-models');
      await page.getByRole('group', { name: 'Autocomplete local' }).getByRole('button').click();
      const menu = page.getByRole('dialog');
      await expect(menu).toBeVisible();
      await expect(menu.getByRole('option')).toHaveCount(10);
      await menu.getByPlaceholder('Search...').fill('2');
      await expect(menu.getByRole('option')).toHaveCount(1);
      await menu.getByRole('option').click();
      await expect(page.getByRole('group', { name: 'Autocomplete local' }).getByText('Option 2')).toBeVisible();
      await page.pause();
    });
  });
});




