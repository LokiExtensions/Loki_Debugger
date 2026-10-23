<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Unit\Observer;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Debugger\Observer\DebugRepository;
use Loki\Debugger\Util\Debugger;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\TestCase;
use stdClass;

class DebugRepositoryTest extends TestCase
{
    public function testComponentDetailsAreAddedToDebugger(): void
    {
        $viewModel = $this->createStub(ComponentViewModelInterface::class);
        $repository = $this->createStub(ComponentRepositoryInterface::class);

        $component = $this->createStub(ComponentInterface::class);
        $component->method('getName')->willReturn('loki-components.test.dummy1');
        $component->method('getViewModel')->willReturn($viewModel);
        $component->method('getRepository')->willReturn($repository);
        $component->method('getValidators')->willReturn(['required']);
        $component->method('getFilters')->willReturn(['security', 'trim']);

        $debugger = new Debugger();
        (new DebugRepository($debugger))->execute(new Observer(['component' => $component]));

        $this->assertSame([
            'component' => 'loki-components.test.dummy1',
            'viewModel' => get_class($viewModel),
            'repository' => get_class($repository),
            'validators' => ['required'],
            'filters' => ['security', 'trim'],
        ], $debugger->getData());
    }

    public function testMissingViewModelAndRepositoryAreSkipped(): void
    {
        $component = $this->createStub(ComponentInterface::class);
        $component->method('getName')->willReturn('loki-components.test.dummy2');
        $component->method('getViewModel')->willReturn(null);
        $component->method('getRepository')->willReturn(null);
        $component->method('getValidators')->willReturn([]);
        $component->method('getFilters')->willReturn([]);

        $debugger = new Debugger();
        (new DebugRepository($debugger))->execute(new Observer(['component' => $component]));

        $this->assertSame([
            'component' => 'loki-components.test.dummy2',
            'validators' => [],
            'filters' => [],
        ], $debugger->getData());
    }

    public function testNonComponentIsIgnored(): void
    {
        $debugger = new Debugger();
        $observer = new DebugRepository($debugger);

        $observer->execute(new Observer(['component' => new stdClass()]));
        $observer->execute(new Observer());

        $this->assertSame([], $debugger->getData());
    }
}
