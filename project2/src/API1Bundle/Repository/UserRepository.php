<?php

namespace  API1Bundle\Repository;

use API1Bundle\Entity\User;


class UserRepository
{
	private $dynamodb;
	private $tableName;
	
	function __construct($dynamodb) 
    { 
        $this->dynamodb = $dynamodb;
        $this->tableName = 'user';
    } 

	// lay thong tin tai khoan theo email
    public function findByEmail ($email){

	    $response = $this->dynamodb->scan([
		   'TableName' => $this->tableName,
		   'ExpressionAttributeValues' =>  [
			   ':val1' => ['S' => $email]
		   ],
		   'FilterExpression' => 'email = :val1',
		  /* 'AttributesToGet' => [ // optional (list of specific attribute names to return)
				'S'=>'username',
			   	'S'=>'email'
		]*/

		]);

		return $response;
    }

	// lay thong tin tai khoan theo username
	public function findByUsername ($username){
        echo "123";
		$response = $this->dynamodb->getItem([

			'TableName' => $this->tableName,
            'Key' => [
                'username' => ['S' => $username]
            ]
		]);
        echo "1234";
		return $response;
	}

	// Tao tai khoan moi
	public function newAccount($email, $username, $password /*, $fullname, $phonenumber, $address, $gender*/) {
		$response = $this->dynamodb->putItem([
			'TableName' => $this->tableName,
			'Item' => [
				'email' => ['S'  => $email],
				'username' => ['S'  => $username],
				//'fullname' => ['S' =>  $fullname],
				'password' => ['S'  => $password],
				//'phonenumber' => ['S' => $phonenumber],
				//'address' => ['S' => $address],
				//'gender' => ['S' => $gender]
			]
		]);
		return $response;
	}

	//update tai khoan
    //update tai khoan
    public function updateUserInfo($fullname, $password, $email, $phone, $address, $gender, $username)
    {
        //$username = $decoded{"username"};
        if ($this->findByUsername($username) === FALSE)
            return FALSE;
        else{
            $response = $this->dynamodb->updateItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'username' => ['S' => $username]
                ],
                'Exists' => ['B'],
                'ExpressionAttributeNames' => [
                    '#fullname' => 'fullname',
                    '#password' => 'password',
                    '#email' => 'email',
                    '#phone' => 'phone',
                    '#address' => 'address',
                    '#gender' => 'gender'
                ],
                'ExpressionAttributeValues' => [
                    ':val1' => ['S' => $fullname],
                    ':val2' => ['S' => $password],
                    ':val3' => ['S' => $email],
                    ':val4' => ['S' => $phone],
                    ':val5' => ['S' => $address],
                    ':val6' => ['S' => $gender],
                ],
                'UpdateExpression' => 'set #fullname = :val1, #password = :val2, #email = :val3,
                                       #phone = :val4, #address = :val5, #gender = :val6',
                'ReturnValues' => 'ALL_NEW'
            ]);
            return $response;
        }
    }

    public function UserLogin($username, $password){
        $response = $this->dynamodb->query([
            'TableName' => $this->tableName,
            'KeyConditionExpression'=> 'username = :v1',
            'FilterExpression' => 'password = :v2',
            'ExpressionAttributeValues'=> [
                ':v1' => ['S' => $username],
                ':v2' => ['S' => $password]]
        ]);

        if($response->get('Count') === 1){
            return ($response->get('Items')[0]);
        }
        return FALSE;
    }
}
