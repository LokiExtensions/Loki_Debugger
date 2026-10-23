<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Integration\Observer;

use Loki\Components\Component\ComponentRegistry;
use Loki\Debugger\Util\Debugger;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class DebugRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->removeSharedInstance(Debugger::class);
    }

    #[AppIsolation(true)]
    public function testRepositoryDispatchAddsComponentDetailsToDebugger(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $component = $objectManager->get(ComponentRegistry::class)
            ->getComponentByName('loki-components.test.dummy1');

        $objectManager->get(EventManager::class)
            ->dispatch('loki_components_repository_dispatch', ['component' => $component]);

        $data = $objectManager->get(Debugger::class)->getData();
        $this->assertSame('loki-components.test.dummy1', $data['component'] ?? null);
        $this->assertSame(get_class($component->getViewModel()), $data['viewModel'] ?? null);
        $this->assertArrayNotHasKey('repository', $data);
        $this->assertSame($component->getValidators(), $data['validators'] ?? null);
        $this->assertSame($component->getFilters(), $data['filters'] ?? null);
    }

    #[AppIsolation(true)]
    public function testBlocksEventAddsBlocksToDebugger(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $blocks = ['loki-components.test.dummy1', 'loki-debugger.debugger'];

        $objectManager->get(EventManager::class)->dispatch('loki_components_blocks', ['blocks' => $blocks]);

        $this->assertSame($blocks, $objectManager->get(Debugger::class)->getData()['blocks'] ?? null);
    }
}
