<h1 align="center">
    Laravel PayMob
    <br>
    <a href="https://packagist.org/packages/ctf0/laravel-paymob"><img src="https://img.shields.io/packagist/v/ctf0/laravel-paymob.svg" alt="Latest Stable Version" /></a> <a href="https://packagist.org/packages/ctf0/laravel-paymob"><img src="https://img.shields.io/packagist/dt/ctf0/laravel-paymob.svg" alt="Total Downloads" /></a>
</h1>

[Paymob](https://paymob.com/en) integration for laravel.

> Currently Supported
>
> - [Card Payments](https://acceptdocs.paymobsolutions.com/docs/card-payments)
> - [Mobile Wallets](https://acceptdocs.paymobsolutions.com/docs/mobile-wallets)

## Installation

- install the package

    ```bash
    composer require ctf0/laravel-paymob
    ```

- publish the package assets with

    ```shell
    php artisan vendor:publish --provider="ctf0\PayMob\PayMobServiceProvider"
    ```

<br>

## Config

- [**config/paymob.php**](./src/config/paymob.php)

<br>

## Setup

- add the package routes to your `routes/web.php` ex.

    ```php
    Route::group([
        'prefix'     => 'orders',
        'as'         => 'order.',
        'middleware' => 'auth',
    ], function () {
        ctf0\PayMob\PayMobRoutes::routes();
    });
    ```

- add `Billable` to the model you will be billing.
- next add `getBillingData()` which should return all the required fields for the order creation, check [paymob requirements](https://acceptdocs.paymobsolutions.com/docs/accept-standard-redirect) for more info.
    + all the **optional** fields has already been taken care of.

    ```php
    use ctf0\PayMob\Integrations\Contracts\Billable;

    class Client implements Billable
    {
        // ...

        public function getBillingData(): array
        {
            return [
                'email'        => $this->email,
                'first_name'   => $this->first_name,
                'last_name'    => $this->last_name,
                'street'       => $this->address,
                'phone_number' => $this->phone_number,
            ];
        }
    }
    ```

<br>

## Usage

### # Normal

- update [`controller`](./src/config/paymob.php) with your own controller, which should have 3 methods
    > you can check [`DummyController`](./src/Controllers/DummyController.php) for a more detailed overview.

    | type | @method    | return                                                                                                                                                                           |
    | ---- | ---------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
    | GET  | `checkOut` | returns the view where the user will press the checkout btn                                                                                                                      |
    | POST | `process`  | get the selected payment type & make a request to paymob server                                                                                                                  |
    | GET  | `complete` | check for the transaction hmac & save it to your server, for more info [check](https://acceptdocs.paymobsolutions.com/docs/transaction-callbacks#transaction-response-callback). |

### # Refund

- all you need to is to call `PayMob::refund` and pass to it the `transaction_id` & `amount_in_pounds` that will be refunded, ex.
    > for more info [check](https://acceptdocs.paymobsolutions.com/docs/refund-transaction)

    ```php
    PayMob::refund(655, 10);
    ```
