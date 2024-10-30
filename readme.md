# CryptoApp

## Overview

CryptoApp is an application for querying information about cryptocurrencies. It uses a service-based architecture and is built with Laravel 5.6. The application can be run in Docker containers for easy development and deployment.

## Technologies

- **PHP 7.2**
- **Laravel 5.6**
- **Postgres 13**


## External Libraries

- **Laravel**: PHP framework used to build the application.
- **Mockery**: Library for creating mocks in unit tests.
- **Carbon**: Library for manipulating dates.
- **Guzzle**: HTTP client used to make requests to external APIs.

## Architectural Decisions

- **Service-based architecture**: Business logic is encapsulated in services, such as `CryptoService`.
- **Dependency injection**: We use dependency injection to manage dependencies between classes.
- **Unit testing**: We use PHPUnit to write unit tests, ensuring code quality.

## Usage

### Prerequisites

- Docker
- Docker Compose

### Settings

1. Clone the repository:
    ```sh
    git clone https://github.com/phtfao/cryptoapp.git
    cd cryptoapp
    ```

1. Run the `docker compose up` command
    
    The application will be available on port 80 with the following routes:
    - http://localhost/api/cryptos/{symbol}/latest

        Where {symbol} is the code of the cryptoasset whose value you want to check.
        This endpoint returns the current value of the cryptoasset.
        ```json
        // http://localhost/api/cryptos/btc/latest
        {
            "name": "bitcoin",
            "symbol": "btc",
            "price": 72233,
            "timestamp": {
                "date": "2024-10-30 04:19:46.671636",
                "timezone_type": 3,
                "timezone": "UTC"
            }
        }
        ```

    - http://localhost/api/cryptos/{symbol}?date={dateTime}

        Where {symbol} is the code of the cryptoasset whose value you want to check and {dateTime} is the date and time of the desired value.
        This endpoint returns the approximate value of the cryptoasset on the date provided.
        ```json
        // http://localhost/api/cryptos/btc?date=2024-10-10+01:23:45
        {
            "name": "bitcoin",
            "symbol": "btc",
            "price": 60597.15,
            "timestamp": {
                "date": "2024-10-10 01:23:45.000000",
                "timezone_type": 3,
                "timezone": "UTC"
            }
        }
        ```
1. To run the unit tests, use the command:
    ```sh
    docker compose exec crypto-app php vendor/bin/phpunit --testdox
    ```

