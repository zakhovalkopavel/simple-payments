<?php
namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Enum\Currency;
use App\Entity\Enum\PaymentType;
use DateTimeImmutable;

class TransactionDto
{
    public function __construct(
        #[Assert\NotBlank]
        public int $payment_id,

        #[Assert\NotBlank]
        public string $transaction,

        #[Assert\NotBlank]
        public string $amount,

        #[Assert\NotBlank]
        #[Assert\Callback([Currency::class, 'validate'])]
        public string $currency,

        #[Assert\NotBlank]
        public string $cardBin,

        #[Assert\NotBlank]
        #[Assert\Callback([PaymentType::class, 'validate'])]
        public string $payment_type,

        #[Assert\NotBlank]
        public DateTimeImmutable $created_at,
    ) {
    }
}