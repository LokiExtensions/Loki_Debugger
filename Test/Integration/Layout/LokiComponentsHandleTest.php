<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Integration\Layout;

use Loki\Debugger\Test\Integration\Traits\LayoutLoader;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class LokiComponentsHandleTest extends TestCase
{
    use LayoutLoader;

    private const HANDLES = ['default', 'loki_components'];

    #[AppIsolation(true)]
    public function testBlocksExist(): void
    {
        $layout = $this->loadLayout(self::HANDLES);

        $blocks = [
            'loki-debugger.script.component.debugger' => [
                'loki.script.component',
                'Loki_Debugger::script/component/debugger-component.phtml',
            ],
            'loki-debugger.json-directive' => [
                'loki.script',
                'Loki_Debugger::script/json-directive.phtml',
            ],
            'loki-debugger.debugger' => [
                'before.body.end',
                'Loki_Debugger::component/debugger.phtml',
            ],
            'loki-debugger.debugger.tab-button.server-dump' => [
                'loki-debugger.debugger',
                'Loki_Debugger::component/debugger/tab-button/generic.phtml',
            ],
            'loki-debugger.debugger.tab-button.components' => [
                'loki-debugger.debugger',
                'Loki_Debugger::component/debugger/tab-button/generic.phtml',
            ],
            'loki-debugger.debugger.tab.server-dump' => [
                'loki-debugger.debugger',
                'Loki_Debugger::component/debugger/tab/server-dump.phtml',
            ],
            'loki-debugger.debugger.tab.components' => [
                'loki-debugger.debugger',
                'Loki_Debugger::component/debugger/tab/components.phtml',
            ],
        ];

        foreach ($blocks as $blockName => [$parentName, $template]) {
            $block = $this->getBlock($layout, $blockName);
            $this->assertSame($parentName, $layout->getParentName($blockName), $blockName);
            $this->assertSame($template, $block->getTemplate(), $blockName);
        }
    }

    #[AppIsolation(true)]
    public function testTabButtonArguments(): void
    {
        $layout = $this->loadLayout(self::HANDLES);

        $serverDumpButton = $this->getBlock($layout, 'loki-debugger.debugger.tab-button.server-dump');
        $this->assertSame('server-dump', $serverDumpButton->getTabName());
        $this->assertSame('Server Dump', $serverDumpButton->getTabLabel());

        $componentsButton = $this->getBlock($layout, 'loki-debugger.debugger.tab-button.components');
        $this->assertSame('components', $componentsButton->getTabName());
        $this->assertSame('Registered Components', $componentsButton->getTabLabel());
    }

    #[AppIsolation(true)]
    public function testDebuggerIsNotAddedWithoutLokiComponentsHandle(): void
    {
        $layout = $this->loadLayout(['default']);
        $this->assertFalse($layout->getBlock('loki-debugger.debugger'));
    }

    #[AppIsolation(true)]
    public function testPageRendersDebugger(): void
    {
        $layout = $this->loadLayout(self::HANDLES);
        $html = $layout->renderElement('before.body.end');

        $this->assertStringContainsString("Alpine.data('LokiComponentsDebugger'", $html);
        $this->assertStringContainsString("Alpine.directive('json'", $html);
        $this->assertStringContainsString('id="loki-debugger-debugger"', $html);
        $this->assertStringContainsString('x-data="LokiComponentsDebugger"', $html);
        $this->assertStringContainsString('id="tab-server-dump"', $html);
        $this->assertStringContainsString('id="tab-components"', $html);
        $this->assertStringContainsString('data-tab-name="server-dump"', $html);
        $this->assertStringContainsString('data-tab-name="components"', $html);
        $this->assertStringContainsString('Loki Debugger', $html);
        $this->assertStringContainsString('Registered Components', $html);
    }

    private function getBlock(LayoutInterface $layout, string $name): Template
    {
        $block = $layout->getBlock($name);
        $this->assertInstanceOf(Template::class, $block, 'Block "' . $name . '" not found');

        return $block;
    }
}
