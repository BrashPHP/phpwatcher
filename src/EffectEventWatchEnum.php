<?php

namespace Brash\PhpWatcher;

enum EffectEventWatchEnum: string
{
    case RENAME = "rename";
    case MODIFY = "modify";
    case CREATE = "create";
    case DESTROY = "destroy";
    case OWNER = "owner";
    case OTHER = "other";
    case ANY = "any";
}
