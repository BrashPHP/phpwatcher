<?php

namespace PhpWatcher;

readonly class AssetRelease
{
    public function __construct(
        public string $name,
        public string $url,
        public OsEnum $osTarget,
        public ArchitectureEnum $architecture
    ) {
    }
}
