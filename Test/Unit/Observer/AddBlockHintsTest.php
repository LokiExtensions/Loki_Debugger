<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Unit\Observer;

use Loki\Components\Config\Config;
use Loki\Debugger\Observer\AddBlockHints;
use Magento\Framework\App\State as AppState;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AddBlockHintsTest extends TestCase
{
    public function testHtmlIsUntouchedWhenDebugIsDisabled(): void
    {
        $transport = $this->execute(false, AppState::MODE_DEVELOPER, "  <div>foo</div>\n");

        $this->assertSame("  <div>foo</div>\n", $transport->getHtml());
    }

    #[DataProvider('getNonDeveloperModes')]
    public function testHtmlIsTrimmedWithoutHintsOutsideDeveloperMode(string $mode): void
    {
        $transport = $this->execute(true, $mode, "\n\t <div>foo</div> \n");

        $this->assertSame('<div>foo</div>', $transport->getHtml());
    }

    public static function getNonDeveloperModes(): array
    {
        return [
            [AppState::MODE_PRODUCTION],
            [AppState::MODE_DEFAULT],
        ];
    }

    public function testHintsAreAddedInDeveloperMode(): void
    {
        $transport = $this->execute(true, AppState::MODE_DEVELOPER, "  <div>foo</div>\n", [
            'name_in_layout' => 'example.block',
            'template_file' => 'Loki_Debugger::example.phtml',
            'sort_order' => '42',
        ]);

        $this->assertSame(
            "<!-- LOKI DEBUGGER: SORT ORDER: 42 -->\n"
            . "<!-- LOKI DEBUGGER: BLOCK: example.block -->\n"
            . "<!-- LOKI DEBUGGER: TEMPLATE: Loki_Debugger::example.phtml -->\n"
            . '<div>foo</div>',
            $transport->getHtml()
        );
    }

    public function testMissingSortOrderIsCastToZero(): void
    {
        $transport = $this->execute(true, AppState::MODE_DEVELOPER, '<div>foo</div>', [
            'name_in_layout' => 'example.block',
        ]);

        $this->assertStringStartsWith("<!-- LOKI DEBUGGER: SORT ORDER: 0 -->\n", (string)$transport->getHtml());
    }

    #[DataProvider('getEmptyHtml')]
    public function testEmptyHtmlIsUntouched(mixed $html): void
    {
        $transport = $this->execute(true, AppState::MODE_DEVELOPER, $html);

        $this->assertSame($html, $transport->getHtml());
    }

    public static function getEmptyHtml(): array
    {
        return [
            [''],
            [" \n\t "],
            [null],
        ];
    }

    private function execute(bool $debug, string $mode, mixed $html, array $blockData = []): DataObject
    {
        $config = $this->createStub(Config::class);
        $config->method('isDebug')->willReturn($debug);

        $appState = $this->createStub(AppState::class);
        $appState->method('getMode')->willReturn($mode);

        $transport = new DataObject(['html' => $html]);
        $event = new Event(['transport' => $transport, 'block' => new DataObject($blockData)]);

        (new AddBlockHints($config, $appState))->execute(new Observer(['event' => $event]));

        return $transport;
    }
}
