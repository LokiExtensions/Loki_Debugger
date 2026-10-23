import {openTestPage} from '@loki/open-test-page';
import {expect, Page} from '@playwright/test';

export const THEMES = ['Magento/luma', 'Hyva/default-csp'];

export const TEST_PAGE_HANDLES = ['loki_components_index_test'];

export const DEBUGGER_ELEMENT_ID = 'loki-debugger-debugger';

export async function configure(page: Page, config: object) {
    const token = process.env.TEST_TOKEN;
    if (!token) {
        throw new Error('TEST_TOKEN is not set');
    }

    const response = await page.request.post(
        '/loki_functional_tests/index/configure?token=' + encodeURIComponent(token),
        {form: {config: JSON.stringify(config)}}
    );

    const body = await response.text();
    expect(response.status(), 'Configure response: ' + body).toBe(200);

    const data = JSON.parse(body);
    expect(data.error, 'Configure error').toBeUndefined();
    expect(data.result?.theme?.errors, 'Theme errors').toBeUndefined();
}

/**
 * Open the test page with a clean debugger state in the local storage
 */
export async function openDebuggerPage(page: Page) {
    await openTestPage(page, TEST_PAGE_HANDLES);
    await page.evaluate(() => localStorage.removeItem('loki-components'));
    await reloadDebuggerPage(page);
}

export async function reloadDebuggerPage(page: Page) {
    await page.reload();
    await waitForDebugger(page);
}

/**
 * The debugger overrides init() of LokiComponentType, so it is not added to the LokiComponents store.
 * Instead, wait until its own init() has selected a tab.
 */
export async function waitForDebugger(page: Page) {
    await page.waitForFunction((elementId) => {
        const Alpine = (window as any).Alpine;
        const element = document.getElementById(elementId);
        if (!Alpine || !element || !Alpine.store('LokiComponents')) {
            return false;
        }

        const data = Alpine.$data(element);
        return !!data && !!data.currentTab;
    }, DEBUGGER_ELEMENT_ID);
}

export async function getStorageValue(page: Page, key: string): Promise<any> {
    // @ts-ignore - global of Loki Components
    return page.evaluate((key) => LokiComponentsStorage.load(key), key);
}

export function getDebugger(page: Page) {
    const root = page.locator('#' + DEBUGGER_ELEMENT_ID);

    return {
        root,
        openButton: root.getByRole('button', {name: 'Loki Debugger', exact: true}),
        closeButton: root.getByRole('button', {name: 'Close', exact: true}),
        toolbar: root.locator('[x-ref="toolbar"]'),
        resizer: root.locator('[x-ref="resizer"]'),
        tabButton: (tabName: string) => root.locator(`button[role="tab"][data-tab-name="${tabName}"]`),
        tab: (tabName: string) => root.locator('#tab-' + tabName),
    };
}

export async function openDebugger(page: Page) {
    const debuggerToolbar = getDebugger(page);
    await debuggerToolbar.openButton.click();
    await expect(debuggerToolbar.toolbar).toBeVisible();

    return debuggerToolbar;
}
