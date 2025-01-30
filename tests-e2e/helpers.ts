import { Page, test, expect } from "@playwright/test";
import { SeedParametersData } from "./site/resources/types/generated";

export async function init(page: Page, props?: { login?: false, seed?: Partial<SeedParametersData> }) {
  await test.step('init', async () => {
    const query = new URLSearchParams({
      login: (props?.login ?? true) ? '1' : '',
      ...props?.seed
        ? Object.fromEntries(Object.entries(props.seed).map(([k, v]) => [`seed[${k}]`, v ? '1' : '']))
        : null,
    });
    const response = await page.goto(`/e2e/init?${query}`, { waitUntil: 'commit' });
    expect(response?.ok(), 'Init ok').toBe(true);
  });
}
