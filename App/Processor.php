<?php

namespace App;

class Processor
{
    /** @var string $inputFile */
    protected $inputFile;

    /** @var array $blocks */
    protected $blocks = [];

    /**
     * Processor's constructor.
     *
     * @param string $inputFile
     */
    public function __construct(string $inputFile)
    {
        $this->inputFile = $inputFile;
    }

    /**
     * Execute processor.
     *
     * @return void
     * 
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $file = fopen($this->inputFile, "r");
        } catch (\Exception $e) {
            throw new \Exception('Invalid Input File.');
        }

        $idx = 0;
        while (!feof($file)) {
            $line = trim(fgets($file));

            # first line is world's size
            if ($idx == 0) {
                $this->populate($line);
            } else {

                # quit aborts the flow execution
                if ($line == 'quit') {
                    break;
                }

                list($verb, $b1, $prep, $b2) = explode(' ', $line);

                if ($b1 == $b2) continue; // ignore a = b

                $p1 = $this->map($b1);
                $p2 = $this->map($b2);

                if ($p1[0] == $p2[0]) continue; // ignore same stack

                if ($verb == 'move' && $prep == 'onto') { // move onto
                    $this->moveOnto($p1, $p2);
                }

                if ($verb == 'move' && $prep == 'over') { // move over
                    $this->moveOver($p1, $p2);
                }

                if ($verb == 'pile' && $prep == 'onto') { // pile onto
                    $this->pileOnto($p1, $p2);
                }

                if ($verb == 'pile' && $prep == 'over') { // pile over
                    $this->pileOver($p1, $p2);
                }
            }
            $idx++;
        }
        fclose($file);

        # show block's final state
        $this->export();
    }

    /**
     * Print the final state of the blocks.
     *
     * @return void
     */
    protected function export()
    {
        for ($i=0; $i<count($this->blocks); $i++) {
            printf("%d:", $i);
            for ($j=0; $j<count($this->blocks[$i]); $j++) {
                printf(" %d", $this->blocks[$i][$j]);
            }
            printf("\n");
        }
    }


    /**
     * Fill initial state of blocks.
     *
     * @param int $size
     * @return void
     * 
     * @throws \Exception
     */
    private function populate(int $size)
    {
        if ($size < 0) {
            throw new \Exception('World Size Must Be Greater Than 0.');
        }
        if ($size > 25) {
            throw new \Exception('World Size Must Be Lower Than 25.');
        }

        for ($i=0; $i<$size; $i++) {
            $this->blocks[$i] = [$i];
        }
    }

    /**
     * Returns column and row of a block (for tracking).
     *
     * @param int $idx
     * @return array
     */
    private function map(int $idx): array
    {
        for ($i=0; $i<count($this->blocks); $i++) {
            for ($j=0; $j<count($this->blocks[$i]); $j++) {
                if ($idx == $this->blocks[$i][$j]) {
                    return [$i, $j];
                }
            }
        }
        return [0, 0];
    }

    /**
     * Reset block to its initial state.
     *
     * @param integer $x
     * @param integer $y
     * @return void
     */
    private function reset(int $x, int $y)
    {
        $idx = $this->blocks[$x][$y];
        $removed = array_splice($this->blocks[$x], $y, 1);
        array_splice($this->blocks[$idx], count($this->blocks[$y])-1, 0, $removed);
    }

    /**
     * Move X Onto Y.
     *
     * @param array $p1
     * @param array $p2
     * @return void
     */
    private function moveOnto(array $p1, array $p2)
    {
        while ($p2[1]+1 != count($this->blocks[$p2[0]])) {
            $this->reset($p2[0], $p2[1]+1);
        }

        while ($p1[1]+1 != count($this->blocks[$p1[0]])) {
            $this->reset($p1[0], $p1[1]+1);
        }

        $offset = count($this->blocks[$p1[0]]) + $p1[1];
        $removed = array_splice($this->blocks[$p1[0]], $offset-1, 1);
        array_splice($this->blocks[$p2[0]], count($this->blocks[$p2[0]]), 0, $removed);
    }

    /**
     * Move X Over Y.
     *
     * @param array $p1
     * @param array $p2
     * @return void
     */
    private function moveOver(array $p1, array $p2)
    {
        while ($p1[1]+1 != count($this->blocks[$p1[0]])) {
            $this->reset($p2[0], $p2[1]+1);
        }

        $offset = count($this->blocks[$p1[0]]) + $p1[1];
        $removed = array_splice($this->blocks[$p1[0]], $offset-1, 1);
        array_splice($this->blocks[$p2[0]], count($this->blocks[$p2[0]]), 0, $removed);
    }

    /**
     * Pile X Onto Y.
     *
     * @param array $p1
     * @param array $p2
     * @return void
     */
    private function pileOnto(array $p1, array $p2)
    {
        while ($p2[1]+1 != count($this->blocks[$p2[0]])) {
            $this->reset($p2[0], $p2[1]+1);
        }

        while ($p1[1] != count($this->blocks[$p1[0]])) {
            $removed = array_splice($this->blocks[$p1[0]], $p1[1], 1);
            array_splice($this->blocks[$p2[0]], count($this->blocks[$p2[0]]), 0, $removed);
        }
    }

    /**
     * Pile X Over Y.
     *
     * @param array $p1
     * @param array $p2
     * @return void
     */
    private function pileOver(array $p1, array $p2)
    {
        while ($p1[1] != count($this->blocks[$p1[0]])) {
            $removed = array_splice($this->blocks[$p1[0]], $p1[1], 1);
            array_splice($this->blocks[$p2[0]], count($this->blocks[$p2[0]]), 0, $removed);
        }
    }
}
