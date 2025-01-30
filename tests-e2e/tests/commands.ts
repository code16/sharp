import { expect, Locator, TestType } from "@playwright/test";
import {
  PlaywrightTestArgs,
  PlaywrightTestOptions,
  PlaywrightWorkerArgs,
  PlaywrightWorkerOptions
} from "playwright/types/test";

export type CommandSuiteArgs = {
  init: () => Promise<void>,
  goto: () => Promise<void>,
  openCommandDropdown: () => Promise<void>,
  contentLocator: Locator,
}

export function commandSuite(test: TestType<PlaywrightTestArgs & PlaywrightTestOptions & CommandSuiteArgs, PlaywrightWorkerArgs & PlaywrightWorkerOptions>) {
  test('form download', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test form command' }).click();
    await page.getByRole('dialog').getByRole('radio', { name: 'Download' }).click();
    const downloadPromise = page.waitForEvent('download');
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    const download = await downloadPromise;
    expect(download.suggestedFilename()).toBe('file.pdf');
    await expect(page.getByRole('dialog')).toBeHidden();
  });
  test('form info', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test form command' }).click();
    await page.getByRole('dialog').getByRole('radio', { name: 'Info' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('alertdialog').getByText('Info message')).toBeVisible();
    await page.getByRole('alertdialog').getByRole('button', { name: 'Ok' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await expect(page.getByRole('alertdialog')).toBeHidden();
  });
  test('form url', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test form command' }).click();
    await page.getByRole('dialog').getByRole('radio', { name: 'Link' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await page.waitForURL('https://example.org');
  });
  test('form reload', async ({ page, init, goto, openCommandDropdown, contentLocator }) => {
    await init();
    await goto();
    let contentText = await contentLocator.textContent();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test form command' }).click();
    await page.getByRole('dialog').getByRole('radio', { name: 'Reload' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await expect(contentLocator).not.toHaveText(contentText);
  });
  test('form refresh', async ({ page, init, goto, openCommandDropdown, contentLocator }) => {
    await init();
    await goto();
    let contentText = await contentLocator.textContent();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test form command' }).click();
    await page.getByRole('dialog').getByRole('radio', { name: 'Refresh' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await expect(contentLocator).not.toHaveText(contentText);
  });
  test('form view', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test form command' }).click();
    await page.getByRole('dialog').getByRole('radio', { name: 'View' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('dialog').frameLocator('iframe').getByText('Command view')).toBeVisible();
    await page.getByRole('dialog').getByRole('button', { name: 'Close'}).click();
    await expect(page.getByRole('dialog')).toBeHidden();
  });
}
