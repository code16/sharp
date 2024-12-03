import { test, expect } from '@playwright/test';
import { seed } from "../helpers";

test('login', async ({ page }) => {
  await seed(page);
  await page.goto('/sharp');

  await expect(page.getByRole('heading', { name: 'Sign in' })).toBeVisible();
  await page.getByRole('button', { name: 'Login' }).click();
  await expect(page.getByRole('alert')).toBeVisible();

  await page.getByRole('textbox', { name: 'Email' }).fill('test@example.org');
  await page.getByRole('textbox', { name: 'Password' }).fill('password');
  await page.getByRole('button', { name: 'Login' }).click();
  await expect(page.getByRole('heading', { name: 'Test models' })).toBeVisible();
});
