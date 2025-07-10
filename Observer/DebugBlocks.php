<?php declare(strict_types=1);

namespace Loki\Debugger\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Loki\Debugger\Util\Debugger;

class DebugBlocks implements ObserverInterface
{
    public function __construct(
        private readonly Debugger $debugger
    ) {
    }

    public function execute(Observer $observer): void
    {
        $blocks = $observer->getBlocks();
        $this->debugger->add('blocks', $blocks);
    }
}
