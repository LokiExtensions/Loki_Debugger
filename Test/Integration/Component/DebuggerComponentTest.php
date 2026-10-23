<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Integration\Component;

use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentRegistry;
use Loki\Debugger\Component\Debugger\DebuggerViewModel;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class DebuggerComponentTest extends TestCase
{
    public function testDebuggerComponentIsRegistered(): void
    {
        $component = $this->getComponentRegistry()->getComponentByName('loki-debugger.debugger');

        $this->assertInstanceOf(Component::class, $component);
        $this->assertSame('loki-debugger.debugger', $component->getName());
        $this->assertInstanceOf(DebuggerViewModel::class, $component->getViewModel());
        $this->assertNull($component->getRepository());
    }

    public function testOtherComponentsTargetTheDebugger(): void
    {
        $component = $this->getComponentRegistry()->getComponentByName('loki-components.test.dummy1');
        $this->assertContains('loki-debugger.debugger', $component->getTargets());
    }

    private function getComponentRegistry(): ComponentRegistry
    {
        return ObjectManager::getInstance()->get(ComponentRegistry::class);
    }
}
