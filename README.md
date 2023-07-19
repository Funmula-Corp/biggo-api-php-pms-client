# BigGo PMS API PHP Client


BigGo PMS API PHP Client is a API written in Javascript.

short future:

- [Getting Started](#getting-started)
  - [Installation](#installation)
  - [Usage](#usage)
  - [Initializing](#initializing)
  - [Accessing BigGo PMS API](#accessing-biggo-pms-api)
- [Typescript](#typescript)
- [License](#license)

## Getting Started

### Installation

Using composer

```shell
$ composer require funmula/biggo-api-php-pms-client
```
### Initializing

To get started, first obtain a client id and secret from BigGo API. Then, use the following code to obtain an API object:

```php
$api = new BiggoPMSAPI(
  '<Your client ID>',
  '<Your client secret>'
  );
```

You can refer to this guide to get the client id and secret

[Funmula-Corp/guide](https://github.com/Funmula-Corp/guide)

### Accessing BigGo PMS API

You can access all BigGo PMS API resources using the api object. Simply use the object obtained from `new BiggoPMSAPI()`. For example:

```php
// Get list of platforms the user has access.
$platformList = $api->getPlatformList();
// Get list of groups in the platform.
$groupList = $api->getGroupList('<Platform ID>')
// Get list of reports in the platform.
$reportList = $api->getReportList('<Platform ID>')
// Get file content or save report as file.
$reportJson = $api->getReport('<Platform ID>', '<Report ID>', 'json')
```

if you need more information, you can refer to this [document](./doc/README.md).
## Typescript

This library supports typescript.

## License

[MIT](./LICENSE)
