import {Page, expect} from "@playwright/test";

export async function init(page: Page, props?: { login?: false }) {
  const response = await page.goto(`/e2e/init?${new URLSearchParams({
    login: (props?.login ?? true) ? '1' : undefined,
  })}`, { waitUntil: 'commit' });
  expect(response?.ok(), 'Seed ok').toBe(true);
}

export async function seed(page: Page) {
  const response = await page.goto(
    `/e2e/seed`,
    { waitUntil: 'commit' }
  );
  expect(response?.ok(), 'Seed ok').toBe(true);
}

export type UserEmail = 'test@example.org';

export async function login(page: Page, email: UserEmail = 'test@example.org') {
  const response = await page.goto(
    `/e2e/login?email=${email}`,
    { waitUntil: 'commit' }
  );
  expect(response?.ok(), 'Login ok').toBe(true);
}
