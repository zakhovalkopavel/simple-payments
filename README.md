# simple-payments

Implementation of Shift4 and ACI payments based on PHP/Symfony


Installation:
    you have to run command below from the root of project:
    make init


For making test request you can send data from Postman to an address:
    -    http://localhost:11089/app/shift4

    -   select "POST" and "raw->JSON"
    
        {"email":"test_mail_120888@gmail.com",
        "cardNumber":"4242424242424242",
        "expMonth":"2",
        "expYear":"2026",
        "cvc":"854",
        "amount":"10.23",
        "currency":"USD"}

Or you can use direct  CURL command:

    curl --location --request POST "http://localhost:11089/app/shift4" \
    --header "Content-Type: application/json" \
    --data "{
    \"email\":\"test_mail_120888@gmail.com\",
    \"cardNumber\":\"4242424242424242\",
    \"expMonth\":\"2\",
    \"expYear\":\"2026\",
    \"cvc\":\"854\",
    \"amount\":\"10.23\",
    \"currency\":\"USD\"
    }"

Response example:
    
    {"transactionId":8,"transaction":"char_Dm8elZwmT5okxoWmeMWAXfdC","amount":"10.23","currency":"USD","cardNumber":"4242424242424242","cardBin":"card_Kc8YE4dvSwrXf0WxGquBVI1p","created_at":"2024-08-06T22:57:29+00:00"}

For view database there is Adminer service:
    http://localhost:11082/
    System: MYSQL
    Server: mysql
    Username: MYSQL_USER
    Password: MYSQL_PASSWORD
    Database: MYSQL_DATABASE
(everything from .env file)

There is also some useful commands in the Makefile...