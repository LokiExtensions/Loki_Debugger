import {test, expect} from '@loki/test';
import {configure, getDebugger, getStorageValue, openDebugger, openDebuggerPage, reloadDebuggerPage, THEMES} from './helpers/configure';

for (const theme of THEMES) {
    test.describe(`Loki Debugger visibility (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openDebuggerPage(page);
        });

        test('opens the toolbar and hides the toggle button', async ({page}) => {
            const debuggerToolbar = await openDebugger(page);

            await expect(debuggerToolbar.openButton).toBeHidden();
            await expect(debuggerToolbar.closeButton).toBeVisible();
            expect(await getStorageValue(page, 'debugger.visible')).toBe(1);
        });

        test('remembers the visibility after a reload', async ({page}) => {
            await openDebugger(page);
            await reloadDebuggerPage(page);

            const debuggerToolbar = getDebugger(page);
            await expect(debuggerToolbar.toolbar).toBeVisible();
            await expect(debuggerToolbar.openButton).toBeHidden();
        });

        test('closes the toolbar again', async ({page}) => {
            const debuggerToolbar = await openDebugger(page);
            await debuggerToolbar.closeButton.click();

            await expect(debuggerToolbar.toolbar).toBeHidden();
            await expect(debuggerToolbar.openButton).toBeVisible();
            expect(await getStorageValue(page, 'debugger.visible')).toBe(0);

            await reloadDebuggerPage(page);
            await expect(getDebugger(page).toolbar).toBeHidden();
        });
    });
}
