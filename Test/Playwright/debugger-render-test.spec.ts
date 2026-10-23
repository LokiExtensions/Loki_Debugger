import {test, expect} from '@loki/test';
import {configure, DEBUGGER_ELEMENT_ID, getDebugger, openDebuggerPage, THEMES} from './helpers/configure';

declare const Alpine: any;

for (const theme of THEMES) {
    test.describe(`Loki Debugger rendering (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
        });

        test('renders the debugger as Loki Component', async ({page}) => {
            await openDebuggerPage(page);

            const debuggerToolbar = getDebugger(page);
            await expect(debuggerToolbar.root).toHaveAttribute('x-data', 'LokiComponentsDebugger');

            const initialData = debuggerToolbar.root.locator(':scope > data[x-ref="initialData"]');
            await expect(initialData).toHaveCount(1);

            const json = JSON.parse(await initialData.textContent() ?? '{}');
            expect(json.blockId).toBe('loki-debugger.debugger');
            expect(json.elementId).toBe(DEBUGGER_ELEMENT_ID);

            const currentTab = await page.evaluate((elementId) => {
                return Alpine.$data(document.getElementById(elementId)).currentTab;
            }, DEBUGGER_ELEMENT_ID);
            expect(currentTab).toBe('server-dump');
        });

        test('shows the toggle button and hides the toolbar by default', async ({page}) => {
            await openDebuggerPage(page);

            const debuggerToolbar = getDebugger(page);
            await expect(debuggerToolbar.openButton).toBeVisible();
            await expect(debuggerToolbar.toolbar).toBeHidden();
        });

        test('renders without Alpine errors or CSP violations', async ({page}) => {
            const consoleErrors: string[] = [];
            page.on('console', message => {
                if (message.type() !== 'error' && message.type() !== 'warning') {
                    return;
                }

                const text = message.text();
                if (/Content Security Policy|Alpine Expression Error|LokiComponentsDebugger/i.test(text)) {
                    consoleErrors.push(text);
                }
            });

            await openDebuggerPage(page);
            await getDebugger(page).openButton.click();
            await expect(getDebugger(page).toolbar).toBeVisible();

            expect(consoleErrors, 'Console errors').toEqual([]);
        });
    });
}
