<?php
//다이나모 디비 연결 키 설정
function DBConnectionConfig(){
    $sdk = new Aws\Sdk([
        'region' => 'ap-northeast-2',
        'version' => 'latest',
        'credentials' => [
            'key' => ' input your access ket ',
            'secret' => ' input your secret key ',
        ]
    ]);
    return $sdk->createDynamoDb();
}