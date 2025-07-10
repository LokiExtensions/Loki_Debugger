<?php declare(strict_types=1);

namespace Loki\Debugger\Component\Debugger;

use Yireo\LokiComponents\Component\ComponentViewModel;

/**
 * @method DebuggerContext getContext()
 */
class DebuggerViewModel extends ComponentViewModel
{
    public function getJsComponentName(): ?string
    {
        return 'LokiComponentsDebugger';
    }

    public function getData(): array
    {
        return $this->getContext()->getDebugger()->getData();
    }
}
