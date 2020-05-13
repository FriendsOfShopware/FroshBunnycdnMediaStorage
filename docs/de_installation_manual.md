- Bitte erstelle ein Backup deines Shops.
- Installiere und aktiviere das Plugin.
- Erstelle eine BunnyCDN-Storage-Zone und verknüpfe diese mit einer Pull-Zone
- Zusätzlich wird empfohlen, den Shop in den Wartungsmodus zu setzen, da während des Prozesses die Dateien aus dem media-Verzeichnis verschoben werden
    
- Füge folgendes deiner config.php hinzu. Achte darauf, dass dies innerhalb des Arrays ist. Passe die Daten innerhalb der Hochkomma mit deinen Daten an:
       
```
'cdn' => [
    'backend' => 'local', /* Ändern Sie dies nach dem Transfer auf bunnycdn */
        'adapters' => [
            'bunnycdn' =>
                [
                'type' => 'bunnycdn',
                'mediaUrl' => 'https://PULLZONE.b-cdn.net/', /* Dies kann auch eine selbstdefinierte Subdomain sein, die bei bunnyCDN hinterlegt wurde */
                'apiUrl' => 'https://storage.bunnycdn.com/STORAGEZONENAME/',
                'apiKey' => 'secret-api-key' /* entspricht dem Password */
                ]
        ]
]
```

Beispiel:
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
        'backend' => 'local', /* Ändern Sie dies nach dem Transfer auf bunnycdn */
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
    
-  Transferiere nun deine aktuelle Medienbibliothek in deinen Storage. Nutze dazu den Befehl auf der Console im Shopware-Verzeichnis: `bin/console sw:media:migrate --from=local --to=bunnycdn`.  
        Sollte es während dessen zu Timeout-Meldungen kommen, können Sie den Prozess erneut starten.  
        Dabei werden die Dateien transferiert und lokal entfernt. Dies ist nur einmal initital notwendig, damit die
        aktuellen Daten transferiert werden. Dies kann je nach Shopgröße etwas Zeit in Anspruch nehmen.  
        Mehr Infos zu diesem Prozess gibt es auch in den Shopware-Dokus: [https://developers.shopware.com/developers-guide/shopware-5-media-service/#file-system-adapters](https://developers.shopware.com/developers-guide/shopware-5-media-service/#file-system-adapters)
    
- Ändere den Wert ``'backend' => 'local'` in `'backend' => 'bunnycdn'` und deaktiviere den Wartungsmodus
- Alle neuen Dateien, die in Shopware hochgeladen werden, werden automatisch nach BunnyCDN transferiert, solange
        das Plugin installiert und aktiviert ist
    
\* Du kannst die Daten mit folgendem Befehl zurück in deinen Shop holen: `bin/console sw:media:migrate --from=bunnycdn --to=local`
