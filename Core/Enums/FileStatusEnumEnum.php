<?php

namespace Core\Enums;

enum FileStatusEnum: int
{
    case NOT_STARTED   = 0;
    case PROCESSING = 1;
    case COMPLETED = 2;
    case FAILED     = 3;
}
