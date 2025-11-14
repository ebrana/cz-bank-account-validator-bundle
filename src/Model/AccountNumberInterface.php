<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Model;

interface AccountNumberInterface
{
    public function getAccountNumber(): ?string;

    public function getBankCode(): ?string;
}
