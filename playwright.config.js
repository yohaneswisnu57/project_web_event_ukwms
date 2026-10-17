import { defineConfig, devices } from '@playwright/test';

const port = 8123;
const baseURL = `http://127.0.0.1:${port}`;

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: [['list'], ['html', { open: 'never' }]],
    use: {
        baseURL,
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
    },
    projects: [
        { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
        { name: 'mobile', use: { ...devices['Pixel 7'] } },
    ],
    /**
     * APP_ENV=e2e makes Laravel load .env.e2e (SQLite, array mailer, sync queue)
     * instead of the production .env. `artisan serve` only passes APP_ENV through
     * to its PHP worker, so every other setting must live in that file.
     */
    webServer: {
        command: `touch database/e2e.sqlite && php artisan migrate:fresh --seed --force && php artisan serve --host=127.0.0.1 --port=${port}`,
        url: baseURL,
        env: { APP_ENV: 'e2e' },
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
    },
});
