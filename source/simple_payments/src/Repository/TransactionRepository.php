<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\TransactionDto;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function saveTransaction(TransactionDto $transactionDto,EntityManagerInterface $entityManager) : int {
        $transaction = new Transaction();
        $transaction->setAmount($transactionDto->amount);
        $transaction->setCardBin($transactionDto->cardBin);
        $transaction->setCreatedAt($transactionDto->created_at);
        $transaction->setCurrency($transactionDto->currency);
        $transaction->setTransaction($transactionDto->transaction);
        $payment = $entityManager->getRepository(Payment::class)->find($transactionDto->payment_id);
        $transaction->setPayment($payment);
        $transaction->setPaymentType($transactionDto->payment_type);

        $entityManager->persist($transaction);
        $entityManager->flush();

        return $transaction->getId();
    }
}
