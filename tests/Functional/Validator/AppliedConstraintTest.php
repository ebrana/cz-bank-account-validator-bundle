<?php

declare(strict_types=1);

namespace Ebrana\CzBankAccountValidatorBundle\Validator;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AppliedConstraintTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function testAppliedConstraint(): void
    {
        $validator = $this->validator;
        $object = new ClassWithConstraintStub('1111111/03ab', 'bagr');
        $violations = $validator->validate($object);

        $this->assertCount(2, $violations);
        $this->assertSame('Error from property', $violations->get(0)->getMessage());
        $this->assertSame('Error from getter', $violations->get(1)->getMessage());
    }

    public function testAppliedConstraintNullValid(): void
    {
        $validator = $this->validator;
        $object = new ClassWithConstraintStub();
        $violations = $validator->validate($object);

        $this->assertCount(0, $violations);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);
        $this->validator = $validator;
    }
}

final class ClassWithConstraintStub
{
    public function __construct(
        #[AccountNumberValid(invalidFormatMessage: 'Error from property')]
        public ?string $accountNumber = null,
        public ?string $number = null,
    ) {
    }

    #[AccountNumberValid(invalidFormatMessage: 'Error from getter')]
    public function getNumber(): ?string
    {
        return $this->number;
    }
}
