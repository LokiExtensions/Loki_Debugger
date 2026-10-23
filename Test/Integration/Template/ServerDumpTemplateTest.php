<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Integration\Template;

use Loki\Debugger\Test\Integration\Traits\LayoutLoader;
use Loki\Debugger\Util\Debugger;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class ServerDumpTemplateTest extends TestCase
{
    use LayoutLoader;

    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->removeSharedInstance(Debugger::class);
    }

    #[AppIsolation(true)]
    public function testServerDumpContainsDebuggerData(): void
    {
        $debugger = Bootstrap::getObjectManager()->get(Debugger::class);
        $debugger->add('component', 'loki-debugger-test-component');
        $debugger->add('blocks', ['loki-debugger-test-block']);

        $layout = $this->loadLayout(['default', 'loki_components']);
        $block = $layout->getBlock('loki-debugger.debugger');
        $this->assertInstanceOf(AbstractBlock::class, $block);
        $html = (string)$block->toHtml();

        $this->assertMatchesRegularExpression('#<pre>.*loki-debugger-test-component.*</pre>#s', $html);
        $this->assertStringContainsString('loki-debugger-test-block', $html);
    }

    #[AppIsolation(true)]
    public function testServerDumpIsEmptyArrayWithoutData(): void
    {
        $layout = $this->loadLayout(['default', 'loki_components']);
        $block = $layout->getBlock('loki-debugger.debugger');
        $this->assertInstanceOf(AbstractBlock::class, $block);
        $html = (string)$block->toHtml();

        $this->assertMatchesRegularExpression('#<pre>.*array.*0.*</pre>#s', $html);
        $this->assertStringNotContainsString('loki-debugger-test-component', $html);
    }
}
