# Introduction

This package provides the connector for Magento 2 to integrate with the MageProxy platform for optimized JavaScript bundling, minification and more... To
find out more, visit our [website](https://mageproxy.io).

# Installation

```shell
composer require mageproxy/connector-magento2
bin/magento module:enable Mageproxy_Connector
bin/magento setup:upgrade
```

# Credentials

The extension requires a `Service ID` and `API key` to integrate with our platform. Sign up for a [plan](https://mageproxy.io/#pricing) to obtain these credentials.

# System Requirements

- PHP 7.4 or later
- Magento 2.4.x or later

# Usage

See our dedicated [documentation](https://mageproxy.io/docs) for detailed usage instructions.

# Tests

## Integration
To run the integration tests, make sure your environment is set up correctly to run Magento 2 integration test. Then run
the following command:

```shell
cd dev/tests/integration
../../vendor/bin/phpunit ../../../vendor/mageproxy/connector-magento2/Test/Integration
```

## Production deployment
After installing the package, enable the module and run setup upgrade, then compile and deploy static content, and finally flush caches:

```shell
bin/magento module:enable Mageproxy_Connector
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

# License

This project is licensed under the [Business Source License 1.1](./LICENSE.txt).  
Use is permitted for non-commercial purposes only.  
