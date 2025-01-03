<?php declare(strict_types=1);

namespace Yireo\LokiComponentsDebugger\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponentsDebugger\Util\Debugger;

class DebugRepository implements ObserverInterface
{
    public function __construct(
        private readonly Debugger $debugger
    ) {
    }

    public function execute(Observer $observer): void
    {
        $component = $observer->getComponent();
        if (false === $component instanceof ComponentInterface) {
            return;
        }

        $this->debug('component', $component->getName());

        $viewModel = $component->getViewModel();
        if (is_object($viewModel)) {
            $this->debug('viewModel', get_class($viewModel));
        }

        $repository = $component->getRepository();
        if (is_object($repository)) {
            $this->debug('repository', get_class($repository));
        }

        $this->debug('validators', $component->getValidators());
        $this->debug('filters', $component->getFilters());
    }

    private function debug(string $name, $value): void
    {
        $this->debugger->add($name, $value);
    }
}
