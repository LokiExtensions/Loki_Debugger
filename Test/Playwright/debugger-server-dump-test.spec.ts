import {test, expect} from '@loki/test';
import {configure, getDebugger, openDebugger, openDebuggerPage, THEMES, waitForDebugger} from './helpers/configure';

declare const Alpine: any;

const HTML_ENDPOINT = '/loki_components/index/html';

for (const theme of THEMES) {
    test.describe(`Loki Debugger server dump (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openDebuggerPage(page);
        });

        test('shows the details of the last AJAX update', async ({page}) => {
            await openDebugger(page);
            const serverDump = getDebugger(page).tab('server-dump').locator('pre');
            await expect(serverDump).not.toContainText('loki-components.test.dummy1');

            const responsePromise = page.waitForResponse(response =>
                response.url().includes(HTML_ENDPOINT) && response.request().method() === 'POST'
            );

            await page.evaluate(() => {
                Alpine.store('LokiComponents').get('LokiComponentsTestDummy1').post('playwright-value');
            });

            const response = await responsePromise;
            expect(response.status()).toBe(200);
            expect(await response.text(), 'Debugger is a default target').toContain('id="loki-debugger-debugger"');

            await waitForDebugger(page);
            const updatedServerDump = getDebugger(page).tab('server-dump').locator('pre');
            await expect(updatedServerDump).toContainText('loki-components.test.dummy1');
            await expect(updatedServerDump).toContainText('Loki\\Components\\Component\\ComponentViewModel');
            await expect(updatedServerDump).toContainText('loki-debugger.debugger');
        });
    });
}
