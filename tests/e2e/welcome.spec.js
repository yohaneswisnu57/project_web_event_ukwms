import { expect, test } from '@playwright/test';

test.describe('landing page', () => {
    test('shows the RETINA 2026 hero with registration open', async ({ page }) => {
        await page.goto('/');

        await expect(page).toHaveTitle(/RETINA/);
        await expect(page.getByRole('heading', { level: 1 })).toHaveText(/RETINA\s*2026/);
        await expect(page.getByText('Pendaftaran dibuka')).toBeVisible();
    });

    test('hero CTA leads to the registration form', async ({ page }) => {
        await page.goto('/');

        await page.getByRole('link', { name: /Amankan kursimu/ }).click();

        await expect(page).toHaveURL(/\/daftar$/);
        await expect(page.getByRole('heading', { level: 1, name: 'Formulir pendaftaran' })).toBeVisible();
    });

    test('has no horizontal overflow', async ({ page }) => {
        await page.goto('/');

        const overflows = await page.evaluate(
            () => document.documentElement.scrollWidth > document.documentElement.clientWidth,
        );

        expect(overflows).toBe(false);
    });
});

test('admin panel redirects guests to the login page', async ({ page }) => {
    await page.goto('/admin');

    await expect(page).toHaveURL(/\/admin\/login$/);
    await expect(page.getByLabel('Email address')).toBeVisible();
});
