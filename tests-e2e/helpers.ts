import { Page, test, expect } from "@playwright/test";
import { SeedParametersData } from "./site/resources/types/generated";

export type InitOptions = { login?: false, seed?: Partial<SeedParametersData>, session?: Record<string, string> };

export async function init(page: Page, options?: InitOptions) {
  await test.step('init', async () => {
    const query = new URLSearchParams({
      login: (options?.login ?? true) ? '1' : '',
      ...options?.seed
        ? Object.fromEntries(Object.entries(options.seed).map(([k, v]) => [`seed[${k}]`, v ? '1' : '']))
        : null,
      ...options?.session
        ? Object.fromEntries(Object.entries(options.session).map(([k, v]) => [`session[${k}]`, v]))
        : null,
    });
    const response = await page.goto(`/e2e/init?${query}`, { waitUntil: 'commit' });
    expect(response?.ok(), 'Init ok').toBe(true);
  });
}
