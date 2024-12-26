<?php

namespace Brash\PhpWatcher;


final class SystemDetector
{
    public function detectOs(): OsEnum
    {
        return OsEnum::tryFrom(PHP_OS_FAMILY) ?? OsEnum::UNKNOWN;
    }

    public function detectArchitecture(): ArchitectureEnum
    {
        return php_uname("m") === "x86_64" ? ArchitectureEnum::X86 : ArchitectureEnum::ARM;
    }
}
