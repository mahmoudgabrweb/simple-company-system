<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $path = database_path("seeders/MainModules");
        $namespace = "Database\\Seeders\\MainModules\\";

        $recursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $seeders = [];

        foreach ($recursiveIterator as $file) {
            $relative = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getPathName());
            $relative = substr($relative, 0, -4);
            $fqcn = $namespace . str_replace(['/', '\\'], '\\', $relative);

            if (class_basename($fqcn) === "DatabaseSeeder") continue;

            if (class_exists($fqcn) && is_subclass_of($fqcn, Seeder::class)) {
                $seeders[$file->getPathName()] = $fqcn;
            }
        }

        ksort($seeders, SORT_NATURAL);

        $files = array_values($seeders);

        foreach ($files as $one) {
            $this->call($one);
        }
    }
}
