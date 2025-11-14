<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Tests\Validator;

use Ebrana\CzBankAccountValidatorBundle\Model\AccountNumberInterface;
use Ebrana\CzBankAccountValidatorBundle\Provider\BankCodesProvider;
use Ebrana\CzBankAccountValidatorBundle\Service\BankAccountNumberValidator;
use Ebrana\CzBankAccountValidatorBundle\Validator\AccountNumberValid;
use Ebrana\CzBankAccountValidatorBundle\Validator\AccountNumberValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<AccountNumberValidator>
 */
class AccountNumberValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @debug make test1 f=testEmptyAccountNumberIsValid
     */
    public function testEmptyAccountNumberIsValid(): void
    {
        $this->validator->validate(new EntityWithAccountNumberStub(), new AccountNumberValid());
        $this->assertNoViolation();
    }

    /**
     * @debug make test1 f=testConstraintWithoutInterface
     */
    public function testConstraintWithoutInterface(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validate(new \stdClass(), new AccountNumberValid());
    }

    /**
     * @debug make test1 f=testAccountNumberViolation
     */
    #[DataProvider('provideInvalidDataViolatingAccount')]
    public function testAccountNumberViolation(string $expectedMessage, ?string $accountNumber, ?string $bankCode): void
    {
        $company = new EntityWithAccountNumberStub();
        $company->accountNumber = $accountNumber;
        $company->bankCode = $bankCode;

        $this->validator->validate($company, new AccountNumberValid());

        $this->buildViolation($expectedMessage)->atPath('property.path.accountNumber')->assertRaised();
    }

    /**
     * @debug make test1 f=testBankCodeViolation
     */
    #[DataProvider('provideInvalidDataViolatingBankCode')]
    public function testBankCodeViolation(string $expectedMessage, ?string $accountNumber, ?string $bankCode): void
    {
        $company = new EntityWithAccountNumberStub();
        $company->accountNumber = $accountNumber;
        $company->bankCode = $bankCode;

        $this->validator->validate($company, new AccountNumberValid());

        $this->buildViolation($expectedMessage)->atPath('property.path.bankCode')->assertRaised();
    }

    /**
     * @return iterable<string, array<string, ?string>>
     */
    public static function provideInvalidDataViolatingBankCode(): iterable
    {
        yield 'filledAccountMissingCode' => [
            'expectedMessage' => 'Vyberte kód banky!',
            'accountNumber' => '3355550267',
            'bankCode' => null,
        ];
        yield 'codeDoesNotExist' => [
            'expectedMessage' => 'Banku s kódem "1234" nemáme v evidenci!',
            'accountNumber' => '3355550267',
            'bankCode' => '1234',
        ];
    }

    /**
     * @return iterable<string, array<string, ?string>>
     */
    public static function provideInvalidDataViolatingAccount(): iterable
    {
        yield 'filledCodeMissingAccount' => [
            'expectedMessage' => 'Vyplňte číslo účtu!',
            'accountNumber' => null,
            'bankCode' => '3030',
        ];
        yield 'invalidFormat' => [
            'expectedMessage' => 'Číslo účtu je ve špatném formátu! Pokud je vč. prefixu, tak musí být oddělený pomlčkou! Např. 11-001111111.',
            'accountNumber' => '1a2-x78',
            'bankCode' => '3030',
        ];
        yield 'wrongPrefixChecksum' => [
            'expectedMessage' => 'Číslo účtu není validní!',
            'accountNumber' => '375-3355550267',
            'bankCode' => '3030',
        ];
        yield 'wrongBaseChecksum' => [
            'expectedMessage' => 'Číslo účtu není validní!',
            'accountNumber' => '3355550268',
            'bankCode' => '3030',
        ];
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new AccountNumberValidator(new BankAccountNumberValidator(new BankCodesProvider()));
    }
}

final class EntityWithAccountNumberStub implements AccountNumberInterface
{
    public ?string $accountNumber = null;
    public ?string $bankCode = null;

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }
}
