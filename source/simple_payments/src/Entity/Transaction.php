<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enum\Currency;
use App\Entity\Enum\PaymentType;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $transaction = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column]
    //#[ORM\Column(type: 'string',  enumType: Currency::class)]
    private ?string $currency = null;

    #[ORM\Column(length: 255)]
    private ?string $cardBin = null;

    #[ORM\OneToOne(targetEntity: Payment::class, inversedBy: 'transaction')]
    #[ORM\JoinColumn(name: 'payment_id', referencedColumnName: 'id')]
    private ?Payment $payment = null;

    #[ORM\Column]
    //#[ORM\Column(type: 'string',  enumType: PaymentType::class)]
    private ?string $paymentType = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransaction(): ?string
    {
        return $this->transaction;
    }

    public function setTransaction(string $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCardBin(): ?string
    {
        return $this->cardBin;
    }

    public function setCardBin(string $cardBin): static
    {
        $this->cardBin = $cardBin;

        return $this;
    }

    public function getPayment(): Collection
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): static
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
