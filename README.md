# BunnyCDN Adapter for Shopware

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

The BunnyCDN adapter allows you to manage your media files in shopware on a bunnyCDN-Storage.


## Install

Download the plugin from the release page and enable it in shopware.

## Usage

Update your `config.php` in your root directory and fill in your own values

```php
'cdn' => [
    'backend' => 'bunnycdn',
    'adapters' => [
        'bunnycdn' =>
            [
                'type' => 'bunnycdn',
                'mediaUrl' => 'https://example.b-cdn.net/',
                'apiUrl' => 'https://storage.bunnycdn.com/example/',
                'apiKey' => 'secret-api-key'
            ]
     ]
]
```

## Migration
`bin/console sw:media:migrate --from=local --to=bunnycdn`

More info: https://developers.shopware.com/developers-guide/shopware-5-media-service/#file-system-adapters


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
