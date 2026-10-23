import {test, expect} from '@loki/test';
import {configure, openDebugger, openDebuggerPage, THEMES} from './helpers/configure';

declare const Alpine: any;

for (const theme of THEMES) {
    test.describe(`Loki Debugger components tab (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openDebuggerPage(page);
        });

        test('lists the registered components that have a value', async ({page}) => {
            await page.evaluate(() => {
                Alpine.store('LokiComponents').get('LokiComponentsTestDummy1').value = 'playwright-value';
            });

            const debuggerToolbar = await openDebugger(page);
            await debuggerToolbar.tabButton('components').click();

            const rows = debuggerToolbar.tab('components').locator('tbody tr');
            await expect(rows).toHaveCount(1);

            const cells = rows.first().locator('td');
            await expect(cells.nth(0)).toContainText('LokiComponentsTestDummy1');
            await expect(cells.nth(1)).toHaveText('loki-components-test-dummy1');
            await expect(cells.nth(2)).toHaveText('loki-components.test.dummy1');
            await expect(cells.nth(3)).toHaveText('playwright-value');

            const details = JSON.parse(await cells.nth(4).locator('code').textContent() ?? '{}');
            expect(Object.keys(details).sort()).toEqual(['valid', 'value', 'visible']);
        });

        test('reloads the components when a component registers', async ({page}) => {
            const debuggerToolbar = await openDebugger(page);
            await debuggerToolbar.tabButton('components').click();

            const rows = debuggerToolbar.tab('components').locator('tbody tr');
            await expect(rows).toHaveCount(0);

            await page.evaluate(() => {
                Alpine.store('LokiComponents').get('LokiComponentsTestDummy2').value = 'registered-value';
                document.dispatchEvent(new CustomEvent('loki-components.component-register'));
            });

            await expect(rows).toHaveCount(1);
            await expect(rows.first()).toContainText('loki-components.test.dummy2');
        });
    });
}
