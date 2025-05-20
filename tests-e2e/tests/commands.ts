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
  test('form', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test form command' }).click();
    await page.getByRole('dialog').getByRole('checkbox', { name: 'Require text field' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('dialog').getByText('Text field is required.')).toBeVisible();
    await page.getByRole('dialog').getByRole('textbox', { name: 'Text' }).fill('Test text');
    await page.getByRole('dialog').getByRole('radio', { name: 'Info' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('alertdialog').getByText('Info message : Test text')).toBeVisible();
    await page.getByRole('alertdialog').getByRole('button', { name: 'Ok' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await expect(page.getByRole('alertdialog')).toBeHidden();
  });
  test('download', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    const downloadPromise = page.waitForEvent('download');
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test download command' }).click();
    const download = await downloadPromise;
    expect(download.suggestedFilename()).toBe('file.pdf');
  });
  test('info', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test info command' }).click();
    await expect(page.getByRole('alertdialog').getByText('Info message')).toBeVisible();
    await page.getByRole('alertdialog').getByRole('button', { name: 'Ok' }).click();
    await expect(page.getByRole('alertdialog')).toBeHidden();
  });
  test('link', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test link command' }).click();
    await page.waitForURL('https://example.org', { waitUntil: 'commit' });
  });
  test('refresh', async ({ page, init, goto, openCommandDropdown, contentLocator }) => {
    await init();
    await goto();
    let contentText = await contentLocator.textContent();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test refresh command' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await expect(contentLocator).not.toHaveText(contentText);
  });
  test('reload', async ({ page, init, goto, openCommandDropdown, contentLocator }) => {
    await init();
    await goto();
    let contentText = await contentLocator.textContent();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test reload command' }).click();
    await expect(contentLocator).not.toHaveText(contentText);
  });
  test('view', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test view command' }).click();
    await expect(page.getByRole('dialog').frameLocator('iframe').getByText('Command view')).toBeVisible();
    await page.getByRole('dialog').getByRole('button', { name: 'Close'}).click();
  });
}

export function commandFormResultSuite(test: TestType<PlaywrightTestArgs & PlaywrightTestOptions & CommandSuiteArgs, PlaywrightWorkerArgs & PlaywrightWorkerOptions>) {
  test('download', async ({ page, init, goto, openCommandDropdown }) => {
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
  test('info', async ({ page, init, goto, openCommandDropdown }) => {
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
  test('link', async ({ page, init, goto, openCommandDropdown }) => {
    await init();
    await goto();
    await openCommandDropdown();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Test form command' }).click();
    await page.getByRole('dialog').getByRole('radio', { name: 'Link' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await page.waitForURL('https://example.org', { waitUntil: 'commit' });
  });
  test('reload', async ({ page, init, goto, openCommandDropdown, contentLocator }) => {
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
  test('refresh', async ({ page, init, goto, openCommandDropdown, contentLocator }) => {
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
  test('view', async ({ page, init, goto, openCommandDropdown }) => {
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
