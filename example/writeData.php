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

require_once($_SERVER['DOCUMENT_ROOT'] . '/aws/aws-autoloader.php');
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

//수정을 하고자 하는 데이터를 정확히 지목해줍시다.
//단건 수정에 대해서만 지원을 해줍니다. 데이터 일괄처리는 작동하지 않습니다.
$year = 2015;
$title = 'The Big New Movie';

$key = $marshaler->marshalJson('
    {
        "year": ' . $year . ', 
        "title": "' . $title . '"
    }
');


//이 로직에서 수정하고자 하는 타겟 데이터의 형태는 아래와 같습니다.
/*
{
    year => "2015",
    title => "The Big New Movie",
    rating => "9.0",
    info => {
        plot => "Everything",
        actors => { "Larry", "Moe" }
    }
}
*/

//수정할 내용에 대해 각 키값으로 매핑시켜줍니다.
$eav = $marshaler->marshalJson('
    {
        ":r": 5.5 ,
        ":p": "Everything happens all at once.",
        ":a": [ "Larry", "Moe", "Curly" ]
    }
');

//매핑시킨 각 키값을 사용해 해당 컬럼의 데이터를 수정해줍니다.
$params = [
    'TableName' => $tableName,
    'Key' => $key,
    'UpdateExpression' => 'set rating = :r, info.plot=:p, info.actors=:a',
    'ExpressionAttributeValues'=> $eav,
    'ReturnValues' => 'UPDATED_NEW'
];

//설정한 키 값 대로 쓰기 처리 시작
try {
    $result = $dynamodb->updateItem($params);
    echo "Updated item.\n";
    print_r($result['Attributes']);

} catch (DynamoDbException $e) {
    echo "Unable to update item:\n";
    echo $e->getMessage() . "\n";
}
