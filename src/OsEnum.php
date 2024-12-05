<?php

namespace PhpWatcher;

enum OsEnum : string{
    case WINDOWS = 'Windows';
    case BSD = 'BSD' ;
    case DARWIN = 'Darwin' ;
    case SOLARIS = 'Solaris' ;
    case LINUX = 'Linux' ;
    case UNKNOWN = 'Unknown' ;
}

