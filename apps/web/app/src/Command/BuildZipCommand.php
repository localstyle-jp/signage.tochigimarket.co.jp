<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class BuildZipCommand extends Command {
    public function initialize() {
        parent::initialize();
        $this->loadModel('MachineBoxes');
    }

    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser
        ->addArguments([
            'id' => ['required' => true],
        ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $machine_box_id = $args->getArgument('id');

        // ZIP用データ
        $this->MachineBoxes->buildZip($machine_box_id);
    }
}
