<?php

namespace Vortechstudio\LaravelTestGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vortechstudio\LaravelTestGenerator\LaravelTestGenerator
 */
class LaravelTestGenerator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Vortechstudio\LaravelTestGenerator\LaravelTestGenerator::class;
    }
}
