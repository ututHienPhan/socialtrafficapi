<?php
/**
 * Created by PhpStorm.
 * User: Luu Nhu
 * Date: 12/11/2016
 * Time: 4:20 PM
 */
namespace  API1Bundle\Repository;
use API1Bundle\Reference;

class FireRepository
{

    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'fire';
    }

    public function getFireByStatus($status)
    {
        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeValues' => [
                ':val1' => ['S' => $status]
            ],
            'FilterExpression' => 'statusA = :val1',
        ]);

        return $response;
    }

    public function getFire($latitude, $longitude, $status)
    {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#statusA' => 'statusA',
                '#latitude' => 'latitude',
                '#longitude' => 'longitude'

            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['S' => $status],
                ':val2' => ['N' => (string)$latitude],
                ':val3' => ['N' => (string)$longitude]
            ],
            'FilterExpression' => '#statusA = :val1 AND #latitude = :val2 AND #longitude = :val3',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

    //get fire theo id
    public function getFireById($id)
    {

        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ]
        ]);
        return $response;
    }

    //insert new fire
    public function insertFire($id, $latitude, $longitude, $timestart, $status,
                                   $description, $image, $address, $level, $username)
    {
        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S' => $id],
                'latitude' => ['N' => (string)$latitude],
                'longitude' => ['N' => (string)$longitude],
                'address' => ['S' => $address],
                'timestart' => ['S' => $timestart],
                'levelA' => ['S' => $level],
                'desciption' => ['S' => $description],
                'image' => ['S' => $image],
                'statusA' => ['S' => $status],
                'agree' => ['N' => '0'],
                'disagree' => ['N' => '0'],
                'username' => ['S' => $username]

            ]
        ]);
        return $response;
    }

    //update fire by comfirm
    public function updateFireByComfirm($id, $agree, $disagree)
    {
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

    //update fire handled
    public function updateFireHandled($id, $status)
    {
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

    //thong ke hoa hoan
    public function FireStatistical($date){
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

    //thong ke hoa hoan trong 7 ngay
    public function FireStatisticalinWeek($date){
        $result = array();
        $result[0]['date'] = $date;
        $result[0]['count'] = $this->FireStatistical($date);
        for($i = 1; $i < 7; $i++)
        {
            //$strdate = $i;
            $date1 = date('Y/m/d', strtotime('-'.$i.' days', strtotime($date)));
            $result[$i]['date'] = $date1;
            $result[$i]['count'] = $this->FireStatistical($date1);
        }
        return $result;
    }

    //thong ke hoa hoan trong 4 tuan
    public function FireStatisticalin4Week($week){
        ini_set('max_execution_time', 300);
        $week_array = array();
        for($i = 0; $i < 4; $i++){
            $date1 = date('Y/m/d', strtotime('-6 days', strtotime($week)));
            $str = $date1.'-'.$week;
            $week_array[$i]['week'] = $str;
            $week_array[$i]['count'] = 0;
            $week_count = $this->FireStatisticalinWeek($week);
            $sum = 0;
            foreach($week_count as $item) {
                $sum += $item['count'];
            }
            $week_array[$i]['count'] = $sum;
            $week = date('Y/m/d', strtotime('-1 days', strtotime($date1)));
        }
        return $week_array;
    }

    //thong ke hoa hoan trong nua nam
    public function FireStatisticalinYear($date){
        $month_array = array();
        $month = date('Y/m', strtotime($date));
        $month_array[0]['month'] = $month;
        $month_array[0]['count'] = $this->FireStatistical($month);
        for($i = 1; $i < 6; $i++){
            $month = date('Y/m', strtotime($date.' -'.$i.' month'));
            $month_array[$i]['month'] = $month;
            $month_array[$i]['count'] = $this->FireStatistical($month);
        }
        return $month_array;
    }
}