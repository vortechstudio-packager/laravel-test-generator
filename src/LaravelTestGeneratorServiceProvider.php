<?php

namespace Vortechstudio\LaravelTestGenerator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vortechstudio\LaravelTestGenerator\Commands\LaravelTestGeneratorCommand;

class LaravelTestGeneratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-test-generator')
            ->hasCommand(LaravelTestGeneratorCommand::class);
    }
}
