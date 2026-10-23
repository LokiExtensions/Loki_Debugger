<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Integration\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class AddBlockHintsTest extends TestCase
{
    private const TEMPLATE = 'Loki_Debugger::component/debugger/tab-button/generic.phtml';

    #[AppIsolation(true)]
    #[ConfigFixture('loki_components/general/debug', 1, 'store', 'default')]
    public function testHintsAreAddedWhenDebugIsEnabled(): void
    {
        if (ObjectManager::getInstance()->get(AppState::class)->getMode() !== AppState::MODE_DEVELOPER) {
            $this->markTestSkipped('Block hints are only added in developer mode');
        }

        $html = $this->renderBlock();

        $this->assertStringStartsWith('<!-- LOKI DEBUGGER: SORT ORDER: 0 -->', $html);
        $this->assertStringContainsString('<!-- LOKI DEBUGGER: BLOCK: loki-debugger.test.block -->', $html);
        $this->assertMatchesRegularExpression(
            '#<!-- LOKI DEBUGGER: TEMPLATE: \S+/view/frontend/templates/component/debugger/tab-button/generic\.phtml -->#',
            $html
        );
        $this->assertStringContainsString('data-tab-name="example"', $html);
    }

    #[AppIsolation(true)]
    #[ConfigFixture('loki_components/general/debug', 0, 'store', 'default')]
    public function testNoHintsAreAddedWhenDebugIsDisabled(): void
    {
        $html = $this->renderBlock();

        $this->assertStringNotContainsString('LOKI DEBUGGER', $html);
        $this->assertStringContainsString('data-tab-name="example"', $html);
    }

    private function renderBlock(): string
    {
        $layout = ObjectManager::getInstance()->create(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, 'loki-debugger.test.block', [
            'data' => [
                'template' => self::TEMPLATE,
                'tab_name' => 'example',
                'tab_label' => 'Example',
            ],
        ]);

        return (string)$block->toHtml();
    }
}
