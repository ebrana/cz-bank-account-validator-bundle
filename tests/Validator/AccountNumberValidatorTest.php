<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Tests\Validator;

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
        $this->validator->validate(null, new AccountNumberValid());
        $this->assertNoViolation();
    }

    /**
     * @debug make test1 f=testAccountNumberViolation
     */
    #[DataProvider('provideInvalidDataViolatingAccount')]
    public function testAccountNumberViolation(
        string $expectedMessage,
        string $accountNumber,
        string $expectedPath = 'accountNumber',
    ): void {
        $constraint = new AccountNumberValid(prefixPath: 'prefixPath');
        $this->validator->validate($accountNumber, $constraint);

        $this->buildViolation($expectedMessage)
            ->atPath(sprintf('property.path.%s', $expectedPath))
            ->assertRaised()
        ;
    }

    /**
     * @debug make test1 f=testBankCodeViolation
     */
    #[DataProvider('provideInvalidDataViolatingBankCode')]
    public function testBankCodeViolation(string $expectedMessage, string $accountNumber): void
    {
        $this->validator->validate($accountNumber, new AccountNumberValid(bankCodePath: 'bankCode'));

        $this->buildViolation($expectedMessage)->atPath('property.path.bankCode')->assertRaised();
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidDataViolatingBankCode(): iterable
    {
        yield 'codeDoesNotExist' => [
            'expectedMessage' => 'Banku s kódem "1234" nemáme v evidenci!',
            'accountNumber' => '3355550267/1234',
        ];
    }

    /**
     * @return iterable<string, array<string, string>>
     */
    public static function provideInvalidDataViolatingAccount(): iterable
    {
        yield 'invalidFormat' => [
            'expectedMessage' => 'Číslo účtu je ve špatném formátu! Pokud je vč. prefixu, tak musí být oddělený pomlčkou a bez mezer! Např. 11-001111111/0300.',
            'accountNumber' => '1a2-x78/3030',
        ];
        yield 'wrongPrefixChecksum' => [
            'expectedMessage' => 'Číslo účtu nemá validní prefix!',
            'accountNumber' => '375-3355550267/3030',
            'expectedPath' => 'prefixPath',
        ];
        yield 'wrongBaseChecksum' => [
            'expectedMessage' => 'Číslo účtu není validní!',
            'accountNumber' => '3355550268/3030',
        ];
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new AccountNumberValidator(new BankAccountNumberValidator(new BankCodesProvider()));
    }
}
