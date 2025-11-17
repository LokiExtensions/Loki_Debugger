<?php declare(strict_types=1);

namespace Loki\Debugger\Observer;

use Loki\Components\Config\Config;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddBlockHints implements ObserverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly AppState $appState,
    ) {
    }

    public function execute(Observer $observer): void
    {
        if (false === $this->config->isDebug()) {
            return;
        }

        $transport = $observer->getEvent()->getTransport();
        $block = $observer->getEvent()->getBlock();
        $html = (string)$transport->getHtml();
        $html = trim($html);
        if (empty($html)) {
            return;
        }

        if ($this->isDeveloperMode()) {
            $html = "<!-- LOKI DEBUGGER: TEMPLATE: ".$block->getTemplateFile()." -->\n".$html;
            $html = "<!-- LOKI DEBUGGER: BLOCK: ".$block->getNameInLayout()." -->\n".$html;
            $html = "<!-- LOKI DEBUGGER: SORT ORDER: ".(int)$block->getSortOrder()." -->\n".$html;
        }

        $transport->setHtml($html);
    }

    private function isDeveloperMode(): bool
    {
        return $this->appState->getMode() === AppState::MODE_DEVELOPER;
    }
}
