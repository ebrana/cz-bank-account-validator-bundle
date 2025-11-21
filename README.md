# Bundle for czech account number validation

Inspired by https://gist.github.com/malja/4fbe9b69878fc81dd2dd77c57fc059a9 that is enough if you just need a simple script.

This bundle adds Symfony validation constraint via PHP Attribute

## Usage
Over property, that contains full number as string (i.e. xxx-xxxxxxx/xxxx)
```php
use \Ebrana\CzBankAccountValidatorBundle\Validator\AccountNumberValid;

class Foo {
    #[AccountNumberValid]
    public ?string $accountNumber = null;
}
```
or if your class have parts separated, you can call it over method
```php
use \Ebrana\CzBankAccountValidatorBundle\Validator\AccountNumberValid;

class Foo {
    public ?string $prefix = null;
    public string $accountNumber;
    public string $bankCode;
    //...
    #[AccountNumberValid(bankCodePath: 'bankCode', prefixPath: 'prefix')]
    public function getAccountNumber(): string
    {   
        $number = $this->accountNumber . '/' . $this->bankCode;
        if (null !== $this->prefix) {
            $number = $this->prefix . '-' . $number;
        }        
        
        return $number;
    }
    // ... 
}
```

### Constraint options
You can specify path for specific errors in your constraint (see 2nd example) and also specify your custom messages.

### Bank codes provider
We are using static list of current bank codes. But you can use custom logic to get the most up to date version from https://www.cnb.cz/cs/platebni-styk/ucty-kody-bank/ .

Just use [decorator](https://symfony.com/doc/current/service_container/service_decoration.html) and implement your custom logic 
