import {test, expect} from '@loki/test';
import {configure, getDebugger, getStorageValue, openDebugger, openDebuggerPage, reloadDebuggerPage, THEMES} from './helpers/configure';

for (const theme of THEMES) {
    test.describe(`Loki Debugger resizing (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openDebuggerPage(page);
        });

        test('resizes the toolbar by dragging and remembers the height', async ({page}) => {
            const debuggerToolbar = await openDebugger(page);
            await expect(debuggerToolbar.toolbar).toHaveCSS('height', '100px');

            const box = await debuggerToolbar.resizer.boundingBox();
            expect(box).not.toBeNull();

            const x = box!.x + box!.width / 2;
            const y = box!.y + box!.height / 2;
            await page.mouse.move(x, y);
            await page.mouse.down();
            await page.mouse.move(x, y - 50, {steps: 5});
            await page.mouse.up();

            await expect(debuggerToolbar.toolbar).toHaveCSS('height', '150px');
            expect(await getStorageValue(page, 'debugger.height')).toBe(150);

            await reloadDebuggerPage(page);
            await expect(getDebugger(page).toolbar).toHaveCSS('height', '150px');
        });
    });
}
