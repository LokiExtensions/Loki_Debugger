<?php declare(strict_types=1);

namespace Loki\Debugger\Component\Debugger;

use Loki\Components\Component\ComponentViewModel;

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
