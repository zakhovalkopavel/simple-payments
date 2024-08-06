<?php
namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Enum\Currency;

class ChargeDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string  $email,


        #[Assert\NotBlank]
        public string $cardNumber,

        #[Assert\NotBlank]
        #[Assert\Range(['min'=>1, 'max'=>12])]
        public string $expMonth,

        #[Assert\NotBlank]
        #[Assert\GreaterThanOrEqual(2024)]
        public string $expYear,

        #[Assert\NotBlank]
        public string $cvc,

        #[Assert\NotBlank]
        public string $amount,

        #[Assert\NotBlank]
        #[Assert\Callback([Currency::class, 'validate'])]
        public string $currency,
    ) {
    }
}