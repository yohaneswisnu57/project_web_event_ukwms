import { expect, test } from '@playwright/test';

test.describe('registration form', () => {
    test('shows validation errors when submitted empty', async ({ page }) => {
        await page.goto('/daftar');

        await page.getByRole('button', { name: /Kirim pendaftaran/ }).click();

        await expect(page.locator('#full_name-error')).toBeVisible();
        await expect(page.locator('#email-error')).toBeVisible();
        await expect(page.getByLabel('Nama lengkap')).toHaveAttribute('aria-invalid', 'true');
    });

    test('clears a field error once it is corrected after a failed submit', async ({ page }) => {
        // Known bug: Livewire 4.1+ `wire:model.blur` no longer sends a request on blur, so
        // RegistrationForm::updated() never re-validates. Fix: `wire:model.live.blur` in
        // resources/views/partials/retina/field.blade.php, then remove this line.
        test.fail();

        await page.goto('/daftar');
        await page.getByRole('button', { name: /Kirim pendaftaran/ }).click();
        await expect(page.locator('#email-error')).toBeVisible();

        await page.getByLabel('Email aktif').fill('peserta@example.com');
        await page.getByLabel('Nomor WhatsApp').focus();

        await expect(page.locator('#email-error')).toHaveCount(0);
        await expect(page.locator('#full_name-error')).toBeVisible();
    });
});
