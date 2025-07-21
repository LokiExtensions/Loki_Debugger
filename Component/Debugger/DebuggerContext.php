<?php
declare(strict_types=1);

namespace Loki\Debugger\Component\Debugger;

use Magento\Framework\App\ObjectManager;
use Loki\Components\Component\ComponentContext;
use Loki\Debugger\Util\Debugger;

class DebuggerContext extends ComponentContext
{
    public function getDebugger(): Debugger
    {
        return ObjectManager::getInstance()->get(Debugger::class);
    }

}
