# KiriminAja PHP SDK

[![Total Downloads](https://img.shields.io/packagist/dt/kiriminaja/kiriminaja-php)](https://packagist.org/packages/kiriminaja/kiriminaja-php)
[![Latest Stable Version](https://img.shields.io/packagist/v/kiriminaja/kiriminaja-php)](https://packagist.org/packages/kiriminaja/kiriminaja-php)
[![license](https://img.shields.io/packagist/l/kiriminaja/kiriminaja-php)](LICENSE)

Official PHP SDK for the [KiriminAja](https://kiriminaja.com) logistics API.

## Requirements

- PHP 8.0+
- ext-json

## Installation

```bash
composer require kiriminaja/kiriminaja-php
```

---

## Quick Start

Call `KiriminAjaConfig` once at app startup, then call any service method on the `KiriminAja` facade.

```php
use KiriminAja\Base\Config\KiriminAjaConfig;
use KiriminAja\Base\Config\Cache\Mode;
use KiriminAja\Services\KiriminAja;

KiriminAjaConfig::setMode(Mode::Staging)::setApiTokenKey('YOUR_API_KEY');

// Use any service
$provinces = KiriminAja::getProvince();
```

---

## Config Options

| Method                                       | Description                                             |
| -------------------------------------------- | ------------------------------------------------------- |
| `KiriminAjaConfig::setMode($mode)`           | `Mode::Staging` or `Mode::Production`                   |
| `KiriminAjaConfig::setApiTokenKey($key)`     | Your KiriminAja API key                                 |
| `KiriminAjaConfig::setCacheDirectory($path)` | Custom cache directory (useful if /tmp is not writable) |
| `KiriminAjaConfig::disableCache()`           | Disable file-based caching entirely                     |

```php
// Custom cache directory
KiriminAjaConfig::setCacheDirectory(__DIR__ . '/kiriminaja-cache');

// Or disable caching entirely
KiriminAjaConfig::disableCache();

KiriminAjaConfig::setMode(Mode::Production)::setApiTokenKey('YOUR_API_KEY');
```

---

## Services

### Address

```php
// List all provinces
KiriminAja::getProvince();

// Cities in a province (province_id)
KiriminAja::getCity(5);

// Districts in a city (city_id)
KiriminAja::getDistrict(12);

// Search districts by name
KiriminAja::getDistrictByName("jakarta");
```

---

### Coverage Area & Pricing

```php
use KiriminAja\Models\ShippingPriceData;
use KiriminAja\Models\ShippingPriceInstantData;

// Express shipping rates
KiriminAja::getPrice(new ShippingPriceData(
    origin: 1,
    destination: 2,
    weight: 1000, // grams
    itemValue: 50000,
    insurance: 0,
    courier: ["jne", "jnt"],
));

// Instant (same-day) rates
KiriminAja::getPriceInstant(new ShippingPriceInstantData(
    service: ["instant"],
    itemPrice: 10000,
    originLat: -6.2,
    originLong: 106.8,
    originAddress: "Jl. Sudirman No.1",
    destinationLat: -6.21,
    destinationLong: 106.81,
    destinationAddress: "Jl. Thamrin No.5",
    weight: 1000,
    vehicle: "motor",
    timezone: "Asia/Jakarta",
));

// Full shipping price
KiriminAja::fullShippingPrice(new ShippingFullPriceData(...));
```

---

### Shipping — Express

```php
// Track by order ID
KiriminAja::getTracking("ORDER123");

// Cancel by AWB
KiriminAja::cancelShipment("AWB123456", "Customer request");

// Request pickup
KiriminAja::requestPickup(new RequestPickupData(...));

// Pickup schedules
KiriminAja::getSchedules();
```

---

### Shipping — Instant

```php
// Request instant pickup
KiriminAja::requestPickupInstant($data, ...$packages);

// Find a new driver for an existing order
KiriminAja::findNewDriver("ORDER123");

// Cancel instant order
KiriminAja::cancelShipment("ORDER123", "reason", isInstant: true);
```

---

### Courier / Preference

```php
// Set whitelist expeditions
KiriminAja::setWhiteListExpedition(["jne_reg", "jne_yes"]);

// Set callback URL
KiriminAja::setCallback("https://example.com/webhook");
```

---

### Payment

```php
// Get payment details
KiriminAja::getPayment("PAY123");

// Get instant payment details
KiriminAja::getPayment("PAY123", isInstant: true);
```

---

## Contributing

For any requests, bugs, or comments, please open an [issue](https://github.com/kiriminaja/kiriminaja-php/issues) or [submit a pull request](https://github.com/kiriminaja/kiriminaja-php/pulls).

## Development

```bash
composer install           # install dependencies
vendor/bin/phpunit tests   # run tests
```
