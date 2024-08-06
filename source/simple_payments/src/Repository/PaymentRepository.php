<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Model\PaymentDto;
use App\Model\PaymentUpdateDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function savePayment(PaymentDto $paymentDto,EntityManagerInterface $entityManager) : int {
        $payment = new Payment();
        $payment->setAmount($paymentDto->amount);
        $payment->setCardNumber($paymentDto->cardNumber);
        $payment->setCreatedAt(new DateTimeImmutable());
        $payment->setCurrency($paymentDto->currency);
        $payment->setPaymentType($paymentDto->payment_type);

        $entityManager->persist($payment);
        $entityManager->flush();

        return $payment->getId();
    }
}
