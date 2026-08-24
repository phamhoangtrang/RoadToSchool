<?php

namespace App\Foundation;

use Illuminate\Foundation\PackageManifest as LaravelPackageManifest;

class PackageManifest extends LaravelPackageManifest
{
    /**
     * Build Laravel's package discovery manifest from either Composer 1 or
     * Composer 2's installed.json format.
     */
    public function build()
    {
        $packages = [];

        if ($this->files->exists($path = $this->vendorPath.'/composer/installed.json')) {
            $installed = json_decode($this->files->get($path), true);
            $packages = isset($installed['packages']) ? $installed['packages'] : $installed;
        }

        $ignoreAll = in_array('*', $ignore = $this->packagesToIgnore());

        $this->write(collect($packages)->mapWithKeys(function ($package) {
            return [$this->format($package['name']) => $package['extra']['laravel'] ?? []];
        })->each(function ($configuration) use (&$ignore) {
            $ignore = array_merge($ignore, $configuration['dont-discover'] ?? []);
        })->reject(function ($configuration, $package) use ($ignore, $ignoreAll) {
            return $ignoreAll || in_array($package, $ignore);
        })->filter()->all());
    }
}

