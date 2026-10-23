import {test, expect} from '@loki/test';
import {configure, getDebugger, getStorageValue, openDebugger, openDebuggerPage, reloadDebuggerPage, THEMES} from './helpers/configure';

const HIDDEN_CLASS = /(^|\s)hidden(\s|$)/;
const SELECTED_CLASS = /(^|\s)bg-orange-50(\s|$)/;

for (const theme of THEMES) {
    test.describe(`Loki Debugger tabs (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openDebuggerPage(page);
        });

        test('selects the server dump tab by default', async ({page}) => {
            const debuggerToolbar = await openDebugger(page);

            await expect(debuggerToolbar.tabButton('server-dump')).toHaveText('Server Dump');
            await expect(debuggerToolbar.tabButton('components')).toHaveText('Registered Components');
            await expect(debuggerToolbar.tabButton('server-dump')).toHaveClass(SELECTED_CLASS);
            await expect(debuggerToolbar.tabButton('components')).not.toHaveClass(SELECTED_CLASS);
            await expect(debuggerToolbar.tab('server-dump')).not.toHaveClass(HIDDEN_CLASS);
            await expect(debuggerToolbar.tab('components')).toHaveClass(HIDDEN_CLASS);
        });

        test('switches to the components tab', async ({page}) => {
            const debuggerToolbar = await openDebugger(page);
            await debuggerToolbar.tabButton('components').click();

            await expect(debuggerToolbar.tabButton('components')).toHaveClass(SELECTED_CLASS);
            await expect(debuggerToolbar.tabButton('server-dump')).not.toHaveClass(SELECTED_CLASS);
            await expect(debuggerToolbar.tab('components')).not.toHaveClass(HIDDEN_CLASS);
            await expect(debuggerToolbar.tab('server-dump')).toHaveClass(HIDDEN_CLASS);
            expect(await getStorageValue(page, 'debugger.tab')).toBe('components');
        });

        test('remembers the selected tab after a reload', async ({page}) => {
            const debuggerToolbar = await openDebugger(page);
            await debuggerToolbar.tabButton('components').click();
            await reloadDebuggerPage(page);

            const reloadedToolbar = getDebugger(page);
            await expect(reloadedToolbar.toolbar).toBeVisible();
            await expect(reloadedToolbar.tabButton('components')).toHaveClass(SELECTED_CLASS);
            await expect(reloadedToolbar.tab('components')).not.toHaveClass(HIDDEN_CLASS);
            await expect(reloadedToolbar.tab('server-dump')).toHaveClass(HIDDEN_CLASS);
        });
    });
}
