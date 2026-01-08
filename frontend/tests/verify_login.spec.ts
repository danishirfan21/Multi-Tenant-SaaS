
import { test, expect } from '@playwright/test';

test('login form', async ({ page }) => {
  await page.goto('http://localhost:5173/login');

  // Fill in the login form
  await page.getByLabel('Email address').fill('john@acme.com');
  await page.getByLabel('Password').fill('password123');

  // Click the login button
  await page.getByRole('button', { name: 'Sign in' }).click();

  // Wait for navigation to the dashboard
  await expect(page).toHaveURL('http://localhost:5173/dashboard');

  // Take a screenshot
  await page.screenshot({ path: 'frontend/tests/screenshot.png' });
});
