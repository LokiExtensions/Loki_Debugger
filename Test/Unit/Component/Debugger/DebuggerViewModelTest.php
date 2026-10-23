<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Unit\Component\Debugger;

use Loki\Debugger\Component\Debugger\DebuggerViewModel;
use Loki\Debugger\Util\Debugger;
use PHPUnit\Framework\TestCase;

class DebuggerViewModelTest extends TestCase
{
    public function testGetJsComponentName(): void
    {
        $viewModel = new DebuggerViewModel(new Debugger());
        $this->assertSame('LokiComponentsDebugger', $viewModel->getJsComponentName());
    }

    public function testGetDataReturnsDebuggerData(): void
    {
        $debugger = new Debugger();
        $viewModel = new DebuggerViewModel($debugger);
        $this->assertSame([], $viewModel->getData());

        $debugger->add('component', 'loki-components.test.dummy1');
        $this->assertSame(['component' => 'loki-components.test.dummy1'], $viewModel->getData());
    }
}
