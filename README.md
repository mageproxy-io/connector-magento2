# Introduction

This package provides the connector for Magento 2 to integrate with the MageProxy platform for optimized JavaScript bundling, minification and more... To
find out more, visit our website at [https://mageproxy.com](https://mageproxy.io).

# Installation

```shell
composer require mageproxy/connector-magento2
bin/magento setup:upgrade
```

# Credentials

The extension requires a Service ID and API key to integrate with our platform. Sign up for an account at [https://mageproxy.com](https://mageproxy.com) to obtain these credentials.

# System Requirements

- PHP 7.4 or later
- Magento 2.4.x or later

# Usage

See our dedicated documentation for detailed usage instructions: [https://docs.mageproxy.com](https://docs.mageproxy.com).

# Tests

## Integration
To run the integration tests, make sure your environment is set up correctly to run Magento 2 integration test. Then run
the following command:

```shell
cd dev/tests/integration
../../vendor/bin/phpunit ../../../vendor/mageproxy/connector-magento2/tests/integration
```

# License

This project is licensed under the [Business Source License 1.1](./LICENSE.txt).  
Use is permitted for non-commercial purposes only.  
