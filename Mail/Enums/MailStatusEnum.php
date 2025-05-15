<?php

namespace Mail\Enums;

enum MailStatusEnum: int {
    case DRAFT = 0;
    case NOT_STARTED = 1;
    case PROCESSING = 2;
    case PARTIALLY_SEND = 3;
    case SENT = 4;
}
