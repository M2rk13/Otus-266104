<?php

namespace App\ProjectCascade\Enum;

enum TransactionStatusEnum
{
    public const NEW = 'new';
    public const WAIT = 'wait';
    public const DONE = 'done';
    public const CANCEL = 'cancel';
}
