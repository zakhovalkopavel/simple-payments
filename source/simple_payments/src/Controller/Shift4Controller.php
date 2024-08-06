<?php

namespace App\Controller;

use App\Model\ChargeDto;
use App\Model\PaymentDto;
    use App\Model\PaymentUpdateDto;
    use App\Model\TransactionDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Enum\Currency;
use App\Entity\Enum\PaymentType;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Repository\TransactionRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class Shift4Controller extends AbstractController
{
    private static string $apiUrl = 'https://api.shift4.com/';
    private static array $methods = [
        'createCustomer' => 'customers',
        'createCard' => 'customers/{CUSTOMER_ID}/cards',
        'charges' => 'charges',
    ];
    private static array $currencyMinorUnits = [
        'USD' => 2,
        'EUR' => 2,
        'CHF' => 2,
    ];

    private string $apiKey;

    public function __construct(
        private HttpClientInterface $client,
        private TransactionRepository $transactionRepository,
        private PaymentRepository $paymentRepository,
    ) {
        $this->apiKey = $_ENV['SHIFT4_API_KEY'].':';
    }

    //Charge card
    #[Route('/app/shift4', name: 'app_shift4', methods: ['POST'])]
    public function index(#[MapRequestPayload] ChargeDto $chargeDto, EntityManagerInterface $entityManager): JsonResponse
    {
        $email = $chargeDto->email;
        $cardNumber = $chargeDto->cardNumber;
        $expYear = $chargeDto->expYear;
        $expMonth = $chargeDto->expMonth;
        $cvc = $chargeDto->cvc;
        $currency = $chargeDto->currency;
        $paymentType = 'SHIFT4';//PaymentType::SHIFT4->toString();

        $paymentData = new PaymentDto(
            number_format($chargeDto->amount, 2, '.', ''),
            $currency,
            $cardNumber,
            $paymentType
        );

        $paymentId = $this->paymentRepository->savePayment($paymentData, $entityManager);


        $customer = $this->createCustomer($email);
        if (array_key_exists('id', $customer) ){
            $card = $this->createCard($customer['id'], $cardNumber, $expMonth, $expYear, $cvc);
            if (array_key_exists('id', $card)) {
                $uri = self::$apiUrl.self::$methods['charges'];

                $amount = ceil($chargeDto->amount * pow(10, self::$currencyMinorUnits[$currency]));

                $config = $this->getConfig([
                    'card' => $card['id'],
                    'customerId' => $customer['id'],
                    'amount' => $amount,
                    'currency' => $currency
                ]);

                $charge = $this->client->request('POST', $uri, $config)->toArray();

                $transactionAmount = number_format( $amount/pow(10, self::$currencyMinorUnits[$currency]), 2, '.', '');

                $datetimeImmutable = new DateTimeImmutable();
                $transactionCreatedAt = $datetimeImmutable->setTimestamp($charge['created']);

                $transactionData =  new TransactionDto(
                    $paymentId,
                    $charge['id'],
                    $transactionAmount,
                    $currency,
                    $card['id'],
                    $paymentType,
                    $transactionCreatedAt
                );
                $transactionId = $this->transactionRepository->saveTransaction($transactionData, $entityManager);

                return $this->json([
                    'transactionId'=>$transactionId,
                    'transaction' => $charge['id'],
                    'amount' => $transactionAmount,
                    'currency' => $currency,
                    'cardNumber' => $cardNumber,
                    'cardBin' => $card['id'],
                    'created_at' => $transactionCreatedAt,
                ]);
            }
            else {
                return $this->json(['error' => [ $customer, $card, $chargeDto ]]);
            }
        }
        return $this->json(['error' => [ $customer, $chargeDto ]]);
    }

    //Set request config
    private function getConfig(array $data) : array {
        return [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->apiKey],
            'json' => $data,
        ];
    }

    //Create card entity
    private function createCard(string $customerId, string $cardNumber, int $expMonth, int $expYear, string $cvc) : array {
        $path =str_replace('{CUSTOMER_ID}', $customerId, self::$methods['createCard']);
        $uri = self::$apiUrl.$path;
        $config = $this->getConfig([
            'number' => $cardNumber,
            'expMonth' => $expMonth,
            'expYear' => $expYear,
            'cvc' => $cvc,
        ]);
        return $this->client->request('POST', $uri, $config)->toArray();
    }

    //Create customer entity
    private function createCustomer(string $email) : array {
        $uri = self::$apiUrl.self::$methods['createCustomer'];
        $config = $this->getConfig(['email' => $email]);
        return $this->client->request('POST', $uri, $config)->toArray();
    }
}
