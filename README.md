# simple-payments

Implementation of Shift4 and ACI payments based on PHP/Symfony


Installation:
    you have to run command below from the root of project: 

    cp .env.example .env
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

(everything is from the .env file)

There is also some useful commands in the Makefile... Just some of them:

    make init                                 // initialisation of the project
    make restart                              // restart docker services
    make kill                                 // stop all docker services
    make php-bash                             // open bash into the php docker container, working directory "cd PHP_PROJECT_NAME"
    make setup-env                            // just use ".env.example"
    make create-project                       // creating BLANK project
    make symfony-init-project                 // command of installation of the BLANK project, currently it is not BLANK project
    make symfony-create-migration-and-migrate // create and apply new migration
    make symfony-migrate                      // just fire existing migration
    make symfony-set-env                      // project .env setup from the root .env file
    make symfony-set-files-permissions        // command for fixing files end directories permissions
    make nginx-conf-reload                    // reload nginx configs
