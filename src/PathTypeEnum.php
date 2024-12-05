<?php
namespace PhpWatcher;

enum PathTypeEnum: string {
    case DIR = 'dir';
    case FILE = 'file';
    case HARD_LINK = 'hard_link';
    case SYM_LINK = 'sym_link';
    case WATCHER = 'watcher';
    case OTHER = 'other';
}

