<?php

namespace Tests;

use App\Processor;

use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    public function testInvalidInputFile(): void
    {
        $this->expectException(\Exception::class);

        $processor = new Processor("Tests/FileNotFound");
        $processor->execute();
    }

    public function testInvalidWorldSize(): void
    {
        $this->expectException(\Exception::class);

        $processor = new Processor("Tests/InvalidStub");
        $processor->execute();
    }

    public function testSampleInputFile(): void
    {
        $this->expectOutputString("0: 0\n1: 1 9 2 4\n2:\n3: 3\n4:\n5: 5 8 7 6\n6:\n7:\n8:\n9:\n");

        $processor = new Processor("Tests/ValidStub");
        $processor->execute();
    }
}
