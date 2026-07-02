<?php

declare(strict_types=1);

namespace App\ADT\Release;

final class ChangelogGenerator
{
    /**
     * @return list<string>
     */
    public function fromModuleChangelogs(string $modulesPath): array
    {
        $entries = [];

        foreach (glob($modulesPath . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'CHANGELOG.md') ?: [] as $changelogPath) {
            $moduleName = basename(dirname($changelogPath));
            $content = file_get_contents($changelogPath);

            if ($content === false || trim($content) === '') {
                continue;
            }

            $entries[] = "## {$moduleName}" . PHP_EOL . trim($content);
        }

        return $entries;
    }

    public function compile(string $version, array $entries): string
    {
        $body = implode(PHP_EOL . PHP_EOL, $entries);

        return "# Release {$version}" . PHP_EOL . PHP_EOL . $body . PHP_EOL;
    }
}
