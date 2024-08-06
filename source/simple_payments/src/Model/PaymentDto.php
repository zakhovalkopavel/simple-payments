<?php
namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Enum\Currency;
use App\Entity\Enum\PaymentType;

class PaymentDto
{
    public function __construct(

        #[Assert\NotBlank]
        public string $amount,

        #[Assert\NotBlank]
        #[Assert\Callback([Currency::class, 'validate'])]
        public string $currency,

        #[Assert\NotBlank]
        public string $cardNumber,

        #[Assert\NotBlank]
        #[Assert\Callback([PaymentType::class, 'validate'])]
        public string $payment_type,
    ) {
    }
}