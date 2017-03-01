<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 29/11/2016
 * Time: 11:19 CH
 */
namespace  API1Bundle\Repository;
use API1Bundle\Reference;

class AccidentRepository
{

    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'accident';
    }

    public function getAccidentByStatus($status) {
        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $status]
            ],
            'FilterExpression' => 'statusA = :val1',
        ]);

        return $response;
    }

    public function getAccident($latitude, $longitude, $status) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#statusA' => 'statusA',
                '#latitude' => 'latitude',
                '#longitude' => 'longitude'

            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $status],
                ':val2' => ['N' => (string)$latitude],
                ':val3' => ['N' => (string)$longitude]
            ],
            'FilterExpression' => '#statusA = :val1 AND #latitude = :val2 AND #longitude = :val3',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

    //get accident theo id
    public function getAccidentById($id) {

        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ]
        ]);

        return $response;
    }

    //insert new accident
    public function insertAccident($id, $latitude, $longitude, $timestart, $status,
                                         $description, $image, $licenseplate, $level, $username){

        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S'  => $id],
                'latitude' => ['N'  => (string)$latitude],
                'longitude' => ['N'  => (string)$longitude],
                'licenseplate' => ['S' => $licenseplate],
                'timestart' => ['S'  => $timestart],
                'levelA' => ['S' => $level],
                'desciption' => ['S' => $description],
                'image' => ['S' => $image],
                'statusA' => ['S'  => $status],
                'agree' => ['N' => '0'],
                'disagree' => ['N' => '0'],
                'username' => ['S' => $username]

            ]
        ]);
        return $response;
    }

    //update accident by comfirm
    public function updateAccidentByComfirm($id, $agree, $disagree) {

        $response = $this->dynamodb->updateItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ],
            'Exists' => ['B'],
            'ExpressionAttributeNames' => [
                '#agree' => 'agree',
                '#disagree' => 'disagree'
            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['N' => (string)$agree],
                ':val2' => ['N' => (string)$disagree]
            ],
            'UpdateExpression' => 'set #agree = :val1, #disagree = :val2',
            'ReturnValues' => 'ALL_NEW'
        ]);
        return $response;
    }

    //update accident handled
    public function updateAccidentHandled($id, $status) {

        $response = $this->dynamodb->updateItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ],
            'Exists' => ['B'],
            'ExpressionAttributeNames' => [
                '#status' => 'statusA',
            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['S' => $status],
            ],
            'UpdateExpression' => 'set #status = :val1',
            'ReturnValues' => 'ALL_NEW'
        ]);
        return $response;
    }

    public function getLicenseAccident($status, $licenseplate) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#statusA' => 'statusA',
                '#licenseplate' => 'licenseplate',
            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $status],
                ':val2' => ['S' => $licenseplate],
            ],
            'FilterExpression' => '#statusA = :val1 and #licenseplate = :val2',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

    //thong ke tai nan
    public function AccidentStatistical($date){
        $response = $this->dynamodb->scan(array(
            'TableName' => $this->tableName
        ));

        $count = $response->get('Count');
        $statistical_date = array();
        for ($i = 0; $i < $count; $i++) {
            $timestart = $response->get('Items')[$i]['timestart']['S'];
            if(strpos($timestart, $date) !== false){
                array_push($statistical_date, $timestart);
            }
        }
        return count($statistical_date);
    }

    //thong ke tai nan trong 7 ngay
    public function AccidentStatisticalinWeek($date){
        $result = array();
        $result[0]['date'] = $date;
        $result[0]['count'] = $this->AccidentStatistical($date);
        for($i = 1; $i < 7; $i++)
        {
            //$strdate = $i;
            $date1 = date('Y/m/d', strtotime('-'.$i.' days', strtotime($date)));
            $result[$i]['date'] = $date1;
            $result[$i]['count'] = $this->AccidentStatistical($date1);
        }
        return $result;
    }

    //thong ke tai nan trong 4 tuan
    public function AccidentStatisticalin4Week($week){
        ini_set('max_execution_time', 300);
        $week_array = array();
        for($i = 0; $i < 4; $i++){
            $date1 = date('Y/m/d', strtotime('-6 days', strtotime($week)));
            $str = $date1.'-'.$week;
            $week_array[$i]['week'] = $str;
            $week_array[$i]['count'] = 0;
            $week_count = $this->AccidentStatisticalinWeek($week);
            $sum = 0;
            foreach($week_count as $item) {
                $sum += $item['count'];
            }
            $week_array[$i]['count'] = $sum;
            $week = date('Y/m/d', strtotime('-1 days', strtotime($date1)));
        }
        return $week_array;
    }

    //thong ke tai nan trong nua nam
    public function AccidentStatisticalinYear($date){
        $month_array = array();
        $month = date('Y/m', strtotime($date));
        $month_array[0]['month'] = $month;
        $month_array[0]['count'] = $this->AccidentStatistical($month);
        for($i = 1; $i < 6; $i++){
            $month = date('Y/m', strtotime($date.' -'.$i.' month'));
            $month_array[$i]['month'] = $month;
            $month_array[$i]['count'] = $this->AccidentStatistical($month);
        }
        return $month_array;
    }
}