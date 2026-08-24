import { chromium } from 'playwright-core';

const baseUrl = process.env.APP_URL || 'http://127.0.0.1:8000';
const browser = await chromium.launch({
    executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome',
    headless: true,
});
const errors = [];
const ignoredBrowserWarnings = [
    /permissions policy violation: compute-pressure is not allowed in this document/i,
];

async function smokeRole({ email, password, paths, screenshot }) {
    const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
    const page = await context.newPage();

    page.on('console', (message) => {
        const isKnownExternalWarning = ignoredBrowserWarnings.some((pattern) => pattern.test(message.text()));

        if (message.type() === 'error' && !isKnownExternalWarning) {
            errors.push(`console ${page.url()}: ${message.text()}`);
        }
    });
    page.on('pageerror', (error) => errors.push(`page ${page.url()}: ${error.stack || error.message}`));
    page.on('response', (response) => {
        if (response.status() >= 400) {
            errors.push(`response ${page.url()}: HTTP ${response.status()} ${response.url()}`);
        }
    });

    await page.goto(`${baseUrl}/login`, { waitUntil: 'networkidle' });
    await page.locator('input[name="email"]').fill(email);
    await page.locator('input[name="password"]').fill(password);
    await Promise.all([
        page.waitForURL(/\/home(?:$|\?)/),
        page.locator('.login-form [type="submit"]').click(),
    ]);

    for (const path of paths) {
        const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle' });

        if (!response?.ok()) {
            errors.push(`${path}: HTTP ${response?.status()}`);
        }
    }

    await page.screenshot({ path: screenshot, fullPage: true });
    await context.close();
}

await smokeRole({
    email: process.env.DEMO_ADMIN_EMAIL || 'admin@roadtoschool.local',
    password: process.env.DEMO_ADMIN_PASSWORD || '123456',
    paths: [
        '/admin',
        '/admin/users',
        '/admin/users/2',
        '/admin/users/create_instructor',
        '/admin/courses',
        '/admin/categories',
        '/admin/categories/1/edit',
        '/admin/permissions',
        '/admin/lectures/requests',
        '/admin/conversations/waiting',
    ],
    screenshot: '/tmp/roadtoschool-admin.png',
});

await smokeRole({
    email: process.env.DEMO_INSTRUCTOR_EMAIL || 'instructor@roadtoschool.local',
    password: process.env.DEMO_INSTRUCTOR_PASSWORD || '123456',
    paths: [
        '/instructor',
        '/instructor/courses',
        '/instructor/courses/create',
        '/instructor/courses/1',
        '/instructor/courses/1/lectures/create',
    ],
    screenshot: '/tmp/roadtoschool-instructor.png',
});

await smokeRole({
    email: process.env.DEMO_STUDENT_EMAIL || 'student@roadtoschool.local',
    password: process.env.DEMO_STUDENT_PASSWORD || '123456',
    paths: ['/courses', '/courses/1', '/courses/1/lectures/1'],
    screenshot: '/tmp/roadtoschool-student.png',
});

await browser.close();

if (errors.length) {
    throw new Error(`Browser smoke test failed:\n${errors.join('\n')}`);
}

console.log('Browser smoke test passed for admin, instructor, and student journeys.');
