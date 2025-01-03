import {Page, test, expect} from "@playwright/test";

export async function init(page: Page, props?: { login?: false }) {
  await test.step('init', async () => {
    const response = await page.goto(`/e2e/init?${new URLSearchParams({
      login: (props?.login ?? true) ? '1' : '',
    })}`, { waitUntil: 'commit' });
    expect(response?.ok(), 'Init ok').toBe(true);
  });
}
