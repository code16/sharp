import { test, test as base, expect, Page, TestType } from '@playwright/test';
import { init } from "../helpers";
import { today, getLocalTimeZone } from "@internationalized/date";
import {
  PlaywrightTestArgs,
  PlaywrightTestOptions,
  PlaywrightWorkerArgs,
  PlaywrightWorkerOptions
} from "playwright/types/test";

type SuiteArgs = {
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
  test.describe('root', () => {
    suite(base.extend<SuiteArgs>({
      goto: ({ page }, use) => use(async () => {
        await page.goto('/sharp/s-list/test-models');
      }),
      reload: ({ page }, use) => use(async () => {
        await page.reload();
      }),
    }));
  });
  test.describe('show', () => {
    suite(base.extend<SuiteArgs>({
      goto: ({ page }, use) => use(async () => {
        await page.goto('/sharp/s-list/test-models/s-show/test-models/1');
      }),
      reload: ({ page }, use) => use(async () => {
      }),
    }));
  });
  function suite(test: TestType<PlaywrightTestArgs & PlaywrightTestOptions & SuiteArgs, PlaywrightWorkerArgs & PlaywrightWorkerOptions>) {
    test.describe('filters', () => {
      test('check', async ({ page, goto, reload }) => {
        await init(page, { seed: { entityList: true } });
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
      test('date range', async ({ page, goto, reload }) => {
        await init(page, { seed: { entityList: true } });
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
      test('date range required', async ({ page, goto, reload }) => {
        await init(page, { seed: { entityList: true } });
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
      test('select', async ({ page, goto, reload }) => {
        await init(page, { seed: { entityList: true } });
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
      test('select multiple', async ({ page, goto, reload }) => {
        await init(page, { seed: { entityList: true } });
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
      test('select required', async ({ page, goto, reload }) => {
        await init(page, { seed: { entityList: true } });
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
  }
});
