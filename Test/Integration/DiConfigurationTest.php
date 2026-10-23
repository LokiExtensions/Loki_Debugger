<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Integration;

use Loki\Components\Util\DefaultTargets;
use Loki\Debugger\Util\Debugger;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class DiConfigurationTest extends TestCase
{
    public function testDebuggerIsAddedToDefaultTargets(): void
    {
        $defaultTargets = ObjectManager::getInstance()->create(DefaultTargets::class);
        $targets = $defaultTargets->getTargets();

        $this->assertArrayHasKey('debugger', $targets);
        $this->assertSame('loki-debugger.debugger', $targets['debugger']);
    }

    public function testDebuggerIsSharedInstance(): void
    {
        $objectManager = ObjectManager::getInstance();
        $this->assertSame($objectManager->get(Debugger::class), $objectManager->get(Debugger::class));
    }
}
