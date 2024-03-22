<?php

namespace App\ProjectCascade\Enum;

enum CascadeTransactionStatusEnum
{
    public const WAIT = 'wait';
    public const DONE = 'done';
    public const CANCEL = 'cancel';
}
