<?php

namespace Vortechstudio\LaravelTestGenerator\Commands;

use Illuminate\Console\Command;
use Vortechstudio\LaravelTestGenerator\Generator;

class LaravelTestGeneratorCommand extends Command
{
    protected $signature = 'laravel-test:generate
                            {--filter= : Filter to a specific route prefix, such as /api or /v2/api}
                            {--dir= : Directory to which the test file are to be stored within the feature folder}
                            {--sync= : Whether @depends attribute to be added to each function inside the test file}';

    protected $description = 'Automatically generates unit test cases for this application';


    public function handle(): int
    {
        $options = [
            'directory' => $this->option('dir') ? $this->option('dir') : '',
            'sync' => (bool)$this->option('sync'),
            'filter' => $this->option('filter')
        ];
        $generator = new Generator($options);
        $generator->generate();

        return self::SUCCESS;
    }
}
