<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Unit\Observer;

use Loki\Debugger\Observer\DebugBlocks;
use Loki\Debugger\Util\Debugger;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\TestCase;

class DebugBlocksTest extends TestCase
{
    public function testBlocksAreAddedToDebugger(): void
    {
        $blocks = ['loki-components.test.dummy1', 'loki-debugger.debugger'];
        $debugger = new Debugger();

        (new DebugBlocks($debugger))->execute(new Observer(['blocks' => $blocks]));

        $this->assertSame(['blocks' => $blocks], $debugger->getData());
    }

    public function testMissingBlocksAreAddedAsNull(): void
    {
        $debugger = new Debugger();

        (new DebugBlocks($debugger))->execute(new Observer());

        $this->assertSame(['blocks' => null], $debugger->getData());
    }
}
