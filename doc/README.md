# BigGo PMS API

This is a library for accessing BigGo PMS API.

## First Step

To get started, first obtain an API key and secret from BigGo API. Then, use the following code to obtain an API object:
```php
$api = new BiggoPMSAPI(
  '<Your client ID>',
  '<Your client secret>'
  );

```

## Usage

### Get Platform List
Get list of platforms the user has access.
`$api->getPlatformList();`

* Return `array`

```php
$api->getPlatformList();
```
---

### Get Group List
Get list of groups in the platform.
`$api->getGroupList('<Platform ID>');`

* Return `array`

```php
$api->getGroupList('<Platform ID>');
```
---

### Get Report List
Get list of reports in the platform.
`$api->getReportList('<Platform ID>', [Options])`

* Return `array`

```php
$api->getReportList('<Platform ID>')
```
`Options`
||required|default value|type|description|
|:---:|:---:|:---:|:---:|:---:|
|size||`5000`|int|size of report list returned|
|startIndex||`0`|int|start index of report list returned|
|sort||`desc`|`asc`\|`desc`|sort order based on report create time|
|groupID||`undefined`|string|filter report list by group ID|
|startDate||`undefined`|string|filter report list by report create time|
|endDate||`undefined`|string|filter report list by report create time|

---

### Get Report
Save report as file or get file content.
`$api->getReport('<Platform ID>', '<Report ID>', 'json'|'csv'|'excel', [Options])`

* Return `string`

```php
$api->getReport('<Platform ID>', '<Report ID>', 'json')
```
`Options`
||required|default value|type|description|
|:---:|:---:|:---:|:---:|:---:|
|saveAsFile||`false`|bool|save report as file|
|saveDir||`.`|string|Directory to save file|
|fileName||`<Platform Name>_<Group Name>_<Report Create Time>.<Format>`|string|file name|