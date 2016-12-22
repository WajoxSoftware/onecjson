# Yii2 component for 1c odata json

## Config example

```
'entities' => [
    'class' => '\wajox\onecjson\services\entities\EntitiesManager',
    'adapterClass' => '\wajox\onecjson\services\data\JsonDataProvider',
    'adapterConfig' => [
        'host' => 'http://ts.server.com:8888/db/odata/standard.odata/',
        'user' => '...',
        'password' => '...',
    ],
],
```

