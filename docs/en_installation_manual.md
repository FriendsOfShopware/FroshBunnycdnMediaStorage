- It is recommended, as always, to create a backup of your shop
- Create a BunnyCDN storage zone and link it to a pull zone
- In addition, it is recommended to put the shop into maintenance mode, as the files are moved out of the media directory during the process
- Add the following to your config.php. Make sure that this is inside the array. Adjust the data within the apostrophe with your data:
       
```
'cdn' => [
    'backend' => 'local', /* Change this after the transfer to bunnycdn */
        'adapters' => [
            'bunnycdn' =>
                [
                'type' => 'bunnycdn',
                'mediaUrl' => 'https://PULLZONE.b-cdn.net/', /* This can also be a self-defined subdomain deposited with bunnyCDN */
                'apiUrl' => 'https://storage.bunnycdn.com/STORAGEZONENAME/',
                'apiKey' => 'secret-api-key' /* corresponds to Password */
                ]
        ]
]
```

Example:
```
return array (
    'db' =>
    [
       'host' => 'localhost',
       'port' => '3306',
       'username' => 'user',
       'password' => 'secret',
       'dbname' => 'dbname',
    ],
    'cdn' => [
        'backend' => 'local', /* Change this after the transfer to bunnycdn */
            'adapters' => [
                'bunnycdn' =>
                    [
                    'type' => 'bunnycdn',
                    'mediaUrl' => 'https://testzone.b-cdn.net/',
                    'apiUrl' => 'https://storage.bunnycdn.com/teststor/',
                    'apiKey' => 'secret-api-keyXYZ'
                    ]
            ]
    ]
);
```
    
-  Transfer your current media library to your storage. Use the command on the console in the Shopware directory:  
`bin/console sw:media:migrate --from=local --to=bunnycdn`.  
           If there are timeout messages during this, you can restart the process.  
           The files are transferred and removed locally. This is only necessary once in order for the current data to be transferred. This can take some time depending on the shop size.  
           More information about this process is also available in the Shopware-Documentation: [https://developers.shopware.com/developers-guide/shopware-5-media-service/#file-system-adapters](https://developers.shopware.com/developers-guide/shopware-5-media-service/#file-system-adapters)
    
- Change the value `'backend' => 'local'` to `'backend' => 'bunnycdn'` and deactivate the maintenance mode
- All new files uploaded to Shopware are automatically transferred to BunnyCDN as long as the plugin is installed and activated
    
\* You can transfer you data back to you shop by using command: `bin/console sw:media:migrate --from=bunnycdn --to=local`
