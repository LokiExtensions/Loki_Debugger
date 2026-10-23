<?php declare(strict_types=1);

namespace Loki\Debugger\Test\Unit\Util;

use Loki\Debugger\Util\Debugger;
use PHPUnit\Framework\TestCase;
use stdClass;

class DebuggerTest extends TestCase
{
    public function testDataIsEmptyByDefault(): void
    {
        $this->assertSame([], (new Debugger())->getData());
    }

    public function testAddStoresValuesOfMixedTypes(): void
    {
        $object = new stdClass();
        $debugger = new Debugger();
        $debugger->add('string', 'foobar');
        $debugger->add('array', ['foo', 'bar']);
        $debugger->add('null', null);
        $debugger->add('object', $object);

        $this->assertSame([
            'string' => 'foobar',
            'array' => ['foo', 'bar'],
            'null' => null,
            'object' => $object,
        ], $debugger->getData());
    }

    public function testAddOverwritesExistingName(): void
    {
        $debugger = new Debugger();
        $debugger->add('component', 'first');
        $debugger->add('component', 'second');

        $this->assertSame(['component' => 'second'], $debugger->getData());
    }
}
