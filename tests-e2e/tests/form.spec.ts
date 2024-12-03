import { test, expect } from '@playwright/test';
import { login, seed } from "../helpers";


test('display form', async ({ page }) => {
  await seed(page);
  await login(page);
  await page.goto('/sharp');
  await page.getByRole('link', { name: 'New...' }).click();
  await page.pause();
})
