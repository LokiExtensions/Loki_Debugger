<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Integration\Observer;

use Loki\Debugger\Observer\AddBlockHints;
use Loki\Debugger\Observer\DebugBlocks;
use Loki\Debugger\Observer\DebugRepository;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ConfigInterface as EventConfig;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class ObserverRegistrationTest extends TestCase
{
    #[DataProvider('getObservers')]
    public function testObserverIsRegistered(string $eventName, string $observerName, string $observerClass): void
    {
        $eventConfig = ObjectManager::getInstance()->get(EventConfig::class);
        $observers = $eventConfig->getObservers($eventName);

        $this->assertArrayHasKey($observerName, $observers);
        $this->assertSame($observerClass, ltrim($observers[$observerName]['instance'], '\\'));
    }

    public static function getObservers(): array
    {
        return [
            ['loki_components_repository_dispatch', 'Loki_Debugger::debugRepository', DebugRepository::class],
            ['loki_components_blocks', 'Loki_Debugger::debugBlocks', DebugBlocks::class],
            ['view_block_abstract_to_html_after', 'Loki_Debugger::addBlockHints', AddBlockHints::class],
        ];
    }
}
