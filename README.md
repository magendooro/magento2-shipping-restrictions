# Magendoo ShippingRestrictions

    magendoo/module-shippingrestrictions

A Magento 2.4.x module that provides **whitelist-based shipping method restrictions** by ZIP code. Only explicitly allowed shipping methods are shown to customers based on their delivery address.

- [Magendoo ShippingRestrictions](#magendoo-shippingrestrictions)
  - [Main Functionalities](#main-functionalities)
  - [Installation](#installation)
    - [Type 1: Zip file](#type-1-zip-file)
    - [Type 2: Composer](#type-2-composer)
  - [Configuration](#configuration)
  - [Usage](#usage)
  - [How It Works](#how-it-works)
  - [Specifications](#specifications)


## Main Functionalities

**Shipping Restrictions** allows you to control which shipping carriers are available to customers based on their delivery ZIP code.

**Key Features:**
- **Whitelist-based approach**: Only explicitly allowed carriers are shown
- **ZIP code restrictions**: Configure allowed carriers per ZIP code
- **Admin UI**: Manage restrictions via Magento admin panel
- **GraphQL compatible**: Works seamlessly with GraphQL checkout
- **High performance**: Optimized database queries with composite indexes (< 1ms)
- **Dedicated logging**: Debug information in `/var/log/shipping_restrictions.log`

**Supported Carriers:**
- Flat Rate (`flatrate`)
- Free Shipping (`freeshipping`)
- Table Rate (`tablerate`)
- Custom carriers (via plugin configuration)

## Installation

**Note:** In production, please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Magendoo`
 - Enable the module by running `php bin/magento module:enable Magendoo_ShippingRestrictions`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require magendoo/module-shippingrestrictions`
 - Enable the module by running `php bin/magento module:enable Magendoo_ShippingRestrictions`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

### Admin Configuration

Navigate to **Stores → Configuration → Magendoo → Shipping Restrictions** to enable/disable the module.

**Configuration Options:**
- **Enable Module**: Turn the module on or off (default: enabled)

### Managing Restrictions

Navigate to **System → Shipping Restrictions** to manage ZIP code restrictions.

**Available Actions:**
- **Add New Restriction**: Create a new ZIP + carrier combination
- **Edit Restriction**: Modify existing restrictions
- **Delete Restriction**: Remove restrictions
- **Inline Edit**: Quick edit directly in the grid

**Example Configuration:**

| ZIP Code | Carrier Code  | Result                                      |
|----------|---------------|---------------------------------------------|
| 10001    | flatrate      | Flat Rate allowed for ZIP 10001             |
| 10001    | freeshipping  | Free Shipping allowed for ZIP 10001         |
| 90210    | flatrate      | Flat Rate allowed for ZIP 90210             |

**Result:**
- ZIP 10001: Shows Flat Rate + Free Shipping only
- ZIP 90210: Shows Flat Rate only
- ZIP 99999: Shows no shipping methods (not configured)


## Usage

### For Store Administrators

1. Navigate to **System → Shipping Restrictions**
2. Click **Add New Restriction**
3. Enter ZIP code (e.g., "10001")
4. Enter carrier code (e.g., "flatrate", "freeshipping", "tablerate")
5. Click **Save**

**Example Use Cases:**

- **Restrict expensive shipping to specific areas**: Only allow Free Shipping for certain ZIP codes
- **Premium delivery zones**: Enable Table Rate only for specific ZIP codes
- **International restrictions**: Block certain carriers for specific postal codes

### For Developers

**Database Insertion Example:**

```sql
INSERT INTO magendoo_shippingrestrictions_shippingrestriction (zip_code, carrier_code) VALUES
('10001', 'flatrate'),
('10001', 'freeshipping');
```

**Programmatic Access:**

```php
use Magendoo\ShippingRestrictions\Api\ShippingRestrictionRepositoryInterface;
use Magendoo\ShippingRestrictions\Api\Data\ShippingRestrictionInterfaceFactory;

public function __construct(
    ShippingRestrictionRepositoryInterface $repository,
    ShippingRestrictionInterfaceFactory $restrictionFactory
) {
    $this->repository = $repository;
    $this->restrictionFactory = $restrictionFactory;
}

public function addRestriction(string $zipCode, string $carrierCode): void
{
    $restriction = $this->restrictionFactory->create();
    $restriction->setZipCode($zipCode);
    $restriction->setCarrierCode($carrierCode);
    $this->repository->save($restriction);
}
```


## How It Works

For a comprehensive explanation of the module architecture, plugin system, and database structure, see:

📖 **[docs/ShippingRestrictions/HOW_IT_WORKS.md](../../docs/ShippingRestrictions/HOW_IT_WORKS.md)**

**Quick Overview:**

1. Customer enters shipping address during checkout
2. Magento requests available shipping methods for the ZIP code
3. Plugin intercepts the `collectRates()` method
4. Database is queried for restrictions matching (ZIP code + carrier code)
5. **If restriction exists**: Carrier is ALLOWED
6. **If no restriction exists**: Carrier is BLOCKED
7. Filtered shipping methods are returned to the customer


## Specifications

### Database Schema

**Table:** `magendoo_shippingrestrictions_shippingrestriction`

| Column                   | Type         | Description                          |
|--------------------------|--------------|--------------------------------------|
| shippingrestriction_id   | int(11)      | Primary key (auto-increment)         |
| zip_code                 | varchar(10)  | ZIP/postal code                      |
| carrier_code             | varchar(255) | Carrier identifier                   |

**Indexes:**
- PRIMARY: `shippingrestriction_id`
- INDEX: `zip_code`
- INDEX: `carrier_code`
- **COMPOSITE INDEX**: `(zip_code, carrier_code)` - Performance optimization

### Models & API

**Service Contracts:**
- `ShippingRestrictionRepositoryInterface`: Repository pattern for CRUD operations
- `ShippingRestrictionInterface`: Data interface for restrictions
- `ShippingRestrictionSearchResultsInterface`: Search results interface

**Models:**
- `ShippingRestriction`: Main model class
- `ResourceModel\ShippingRestriction`: Resource model
- `ResourceModel\ShippingRestriction\Collection`: Collection model

**Plugins:**
- `ZipCodeRestrictionPlugin`: Intercepts carrier `collectRates()` method

**Validators:**
- `InputValidator`: Validates and sanitizes ZIP codes and carrier codes

**Helpers:**
- `Config`: Configuration helper for module enable/disable

### Logging

**Log File:** `/var/log/shipping_restrictions.log`

**Log Levels:**
- `debug`: Restriction checks and decisions
- `info`: Successful operations
- `error`: Failures and exceptions
- `critical`: Critical errors

**Example Log Entries:**
```
[2025-11-22 10:30:15] shippingRestrictions.DEBUG: Shipping method allowed by restriction {"carrier":"flatrate","zip_code":"10001"}
[2025-11-22 10:30:16] shippingRestrictions.DEBUG: Shipping method restricted - no matching restriction found {"carrier":"tablerate","zip_code":"10001"}
```
