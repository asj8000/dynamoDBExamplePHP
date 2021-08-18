<?php

/**
 * 이 파일은 aws의 가이드를 참고하여 만들어졌습니다.
 * This file was created by referring to aws' guide.
 * 
 * https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/GettingStarted.PHP.03.html
 * 
 * 이 파일의 저작권 또한 Amazon에 있습니다.
 * The copyright of this code is in Amazon.com
 * 
 * 정확한 실 테스트를 거치지 않은 설명용 로직이기에 확인용으로 사용해주시면 감사하겠습니다.
 */

require $_SERVER['DOCUMENT_ROOT'].'/lib/DBConnectionConfig.php';
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

//dyanmoDB 연결 설정
$sdkConfig = DBConnectionConfig();
$dynamodb = $sdkConfig->createDynamoDb();
//aws SDK를 위한 Json encode 설정
$marshaler = new Marshaler(); 

//설정한 테이블 명을 입력해주세요.
$tableName = 'Movies'; 

//메인키가 '2015' 인 값들을 찾아봅시다.
//검색을 위해 찾고자 하는 데이터와 키값을 설정해줍니다.
$year = 2015;
$key = $marshaler->marshalJson('
    {
        "year": ' . $year . '
    }
');

//만약 원한다면, 아래와 같은 형태로 세컨드 키를 사용할 수 있습니다.
/*
$year = 2015;
$title = 'The Big New Movie';

$key = $marshaler->marshalJson('
    {
        "year": ' . $year . ', 
        "title": "' . $title . '"
    }
');
*/

$params = [
    'TableName' => $tableName,
    'Key' => $key
];

//설정한 키 값 대로 읽기 처리 시작
try {
    $result = $dynamodb->getItem($params);
    print_r($result["Item"]);
} catch (DynamoDbException $e) {
    echo "Unable to get item:\n";
    echo $e->getMessage() . "\n";
}


