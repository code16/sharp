import { test, test as base } from '@playwright/test';
import { commandSuite, CommandSuiteArgs } from "./commands";
import { init } from "../helpers";


test.describe('show', () => {
  test.describe('commands', () => {
    commandSuite(base.extend<CommandSuiteArgs>({
      init: ({ page }, use) => use(async () => {
        await init(page, { seed: { show: true } });
      }),
      goto: ({ page }, use) => use(async () => {
        await page.goto('/sharp/s-list/test-models/s-show/test-models/1');
      }),
      openCommandDropdown: ({ page }, use) => use(async () => {
        await page.getByRole('button', { name: 'Actions' }).first().click();
      }),
      contentLocator: ({ page }, use) => use(page.getByRole('region').first()),
    }));
  });
});
