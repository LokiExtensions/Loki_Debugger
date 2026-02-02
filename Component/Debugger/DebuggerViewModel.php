<?php declare(strict_types=1);

namespace Loki\Debugger\Component\Debugger;

use Loki\Components\Component\ComponentViewModel;
use Loki\Debugger\Util\Debugger;

class DebuggerViewModel extends ComponentViewModel
{
    public function __construct(
        private Debugger $debugger,
    ) {
    }

    public function getJsComponentName(): ?string
    {
        return 'LokiComponentsDebugger';
    }

    public function getData(): array
    {
        return $this->debugger->getData();
    }
}
