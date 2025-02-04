import { expect, test, test as base } from '@playwright/test';
import { commandSuite, CommandSuiteArgs } from "./commands";
import { init } from "../helpers";


test.describe('show', () => {
  test('display show', async ({ page }) => {
    await init(page, { seed: { show: true } });
    await page.goto('/sharp/s-list/test-models/s-show/test-models/1');
    await expect(page.getByRole('heading', { name: 'Example' })).toBeVisible();
  });

  test.describe('commands', () => {
    commandSuite(base.extend<CommandSuiteArgs>({
      init: ({ page }, use) => use(async () => {
        await init(page, { seed: { show: true } });
      }),
      goto: ({ page }, use) => use(async () => {
        await page.goto('/sharp/s-list/test-models/s-show/test-models/1');
      }),
      openCommandDropdown: ({ page }, use) => use(async () => {
        await page
          .getByRole('group', { name: 'Menu for Example' })
          .getByRole('button', { name: 'Actions', exact: true })
          .first()
          .click();
      }),
      contentLocator: ({ page }, use) => use(page.getByRole('region').first()),
    }));
  });

  test('state', async ({ page }) => {
    await init(page, { seed: { show: true } });
    await page.goto('/sharp/s-list/test-models/s-show/test-models/1');
    const stateButton = page.getByRole('group', { name: 'Menu for Example' }).getByRole('button', { name: 'Update state' });
    const actionsButton = page.getByRole('group', { name: 'Menu for Example' }).getByRole('button', { name: 'Actions' });
    await expect(stateButton).toHaveText('Draft');
    await stateButton.click();
    await expect(page.getByRole('menu').getByRole('menuitemcheckbox', { name: 'Draft' })).toBeChecked();
    await page.getByRole('menu').getByRole('menuitemcheckbox', { name: 'Published' }).click();
    await expect(stateButton).toHaveText('Published');
    await page.reload();
    await expect(stateButton).toHaveText('Published');
    await actionsButton.click();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Update state' }).click();
    await expect(page.getByRole('menu').getByRole('menuitemcheckbox', { name: 'Published' })).toBeChecked();
    await page.getByRole('menu').getByRole('menuitemcheckbox', { name: 'Draft' }).click();
    await actionsButton.click();
    await page.getByRole('menu').getByRole('menuitem', { name: 'Update state' }).click();
    await expect(page.getByRole('menu').getByRole('menuitemcheckbox', { name: 'Draft' })).toBeChecked();
    await page.mouse.click(0, 0);
    await expect(stateButton).toHaveText('Draft');
    await page.reload();
    await expect(stateButton).toHaveText('Draft');
  });
});

test.describe('single show', () => {
  test('display single show', async ({ page }) => {
    await init(page, { seed: { show: true } });
    await page.goto('/sharp/s-show/test-models-single');
    await expect(page.getByRole('heading', { name: 'Example' })).toBeVisible();
  });
});
