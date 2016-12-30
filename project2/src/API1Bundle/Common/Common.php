<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 18/11/2016
 * Time: 9:41 SA
 */
namespace  API1Bundle\Common;
class Common
{
    public $RESULT_CODE_SUCCESS = 1;
    public $RESULT_CODE_FAIL = 0;

    // number to evaluate accurate
    public $NUMBER = 3;
    //register account
    public $REGISTERED_ACCOUNT_SUCCESSFULLY = 'Account created successful';
    public $REGISTERED_ACCOUNT_EMAIL_EXISTED = 'Email is used to register';
    public $REGISTERED_ACCOUNT_USERNAME_EXISTED = 'Username is used to register';
    public $REGISTERED_ACCOUNT_FAIL = 'Server too busy';
    public $REGISTERED_ACCOUNT_ERROR_REQUEST = 'Error input';

    //register moto
    public $REGISTER_MOTO_SUCCSESSFULLY = 'Moto created successful';
    public $REGISTER_MOTO_INFO_EXISTED = 'Moto infomation is used to register';
    public $REGISTER_MOTO_FAIL = 'Server too busy';
    public $REGISTER_MOTO_ERROR_REQUEST = 'Error input';
    public $REGISTER_MOTO_ERROR_NOT_FOUND = 'Not found in database';

    //register address
    public $REGISTER_ADDRESS_TOKEN_ERROR = 'Error input';
    public $REGISTER_ADDRESS_SUCCESSFULLY = 'Address created successfully';
    public $REGISTER_ADDRESS_EXISTED = 'This address already has been registered by this account';
    public $REGISTER_ADDRESS_ERROR_INPUT = 'Input data invalid';
    public $REGISTER_ADDRESS_FAIL = 'Register fail';
    public $REGISTER_ADDRESS_TOKEN_ERROR_NOT_FOUND = 'Not found this account in database';

    //repport accident
    public $REPORT_ACCIDENT_SUCCESSFULLY = 'Report accident successfully';
    public $REPORT_ACCIDENT_FAIL = 'Report accident fail';
    public $REPORT_ACCIDENT_ERROR_REQUEST = 'Error input';
    public $REPORT_ACCIDENT_ERROR_NOT_FOUND = 'Not found in database';

    //comfirm accident
    public $COMFIRM_ACCIDENT_SUCCESSULLY = 'Comfirm accident successfully';
    public $COMFIRM_ACCIDENT_FAIL = 'Comfirm accident fail';
    public $COMFIRM_ACCIDENT_ERROR_REQUEST = 'Error input';
    public $COMFIRM_ACCIDENT_ERROR_NOT_FOUND = 'Not found in database';
    public $COMFIRM_ACCIDENT_HANDLED = 'Accident handled';

    //get accident
    public $GET_ACCIDENT_SUCCESSULLY = 'Get accident successfully';
    public $GET_ACCIDENT_FAIL = 'Get accident fail';
    public $GET_ACCIDENT_ERROR_REQUEST = 'Error input';
    public $GET_ACCIDENT_ERROR_NOT_FOUND = 'Not found in database';

    //sign in by token
    public $SIGN_IN_TOKEN_SUCCESSULLY = 'Sign in successfully';
    public $SIGN_IN_TOKEN_FAIL = 'Sign in fail';
    public $SIGN_IN_TOKEN_ERROR_REQUEST = 'Error input';
    public $SIGN_IN_TOKEN_ERROR_NOT_FOUND = 'Not found in database';

    //update user info
    public $UPDATE_INFO_SUCCESSULLY = 'Updated successfully';
    public $UPDATE_INFO_FAIL = 'Update fail';
    public $UPDATE_INFO_TOKEN_ERROR_REQUEST = 'Error input';
    public $UPDATE_INFO_TOKEN_ERROR_NOT_FOUND = 'Not found in database';
    public $UPDATE_INFO_ERROR_INPUT = 'Input data invalid';

    //get accidents local
    public $GET_ACCIDENTS_LOCAL_SUCCESSULLY = 'Get accidents local successfully';
    public $GET_ACCIDENTS_LOCAL_FAIL = 'Get accidents local fail';
    public $GET_ACCIDENTS_LOCAL_ERROR_REQUEST = 'Error input';
    public $GET_ACCIDENTS_LOCAL_ERROR_NOT_FOUND = 'Not found in database';

    //login
    public $LOGIN_SUCCESSFULLY = 'Login Successfully';
    public $LOGIN_FAIL = 'Cannot login, error';
    public $LOGIN_ERROR_INPUT = 'Error input';

    //report accident handled
    public $REPORT_ACCIDENT_HANDLED_SUCCESSFULLY = 'Report accident successfully';
    public $REPORT_ACCIDENT_HANDLED_FAIL = 'Report accident fail';
    public $REPORT_ACCIDENT_HANDLED_ERROR_REQUEST = 'Error input';
    public $REPORT_ACCIDENT_HANDLED_ERROR_NOT_FOUND = 'Not found in database';
    public $REPORT_ACCIDENT_HANDLED = 'Accident handled ago';

    //report accident handled
    public $EVALUATE_ACCURATE_ACCIDENT_SUCCESSFULLY = 'Get result of evaluation accurate accident successfully';
    public $EVALUATE_ACCURATE_ACCIDENT_FAIL = 'Get result of evaluation accurate accident fail';
    public $EVALUATE_ACCURATE_ACCIDENT_ERROR_REQUEST = 'Error input';
    public $EVALUATE_ACCURATE_ACCIDENT_ERROR_NOT_FOUND = 'Not found in database';

    //Lay thong tin nguoi dung
    public $GET_INFO_USER_SUCCESSULLY = 'Get info user successfully';
    public $GET_INFO_USER_FAIL = 'Get info user fail';
    public $GET_INFO_USER_ERROR_REQUEST = 'Error input';
    public $GET_INFO_USER_ERROR_NOT_FOUND = 'Not found in database';

    //repport fire
    public $REPORT_FIRE_SUCCESSFULLY = 'Report fire successfully';
    public $REPORT_FIRE_FAIL = 'Report fire fail';
    public $REPORT_FIRE_ERROR_REQUEST = 'Error input';
    public $REPORT_FIRE_ERROR_NOT_FOUND = 'Not found in database';

    //comfirm fire
    public $COMFIRM_FIRE_SUCCESSULLY = 'Comfirm fire successfully';
    public $COMFIRM_FIRE_FAIL = 'Comfirm fire fail';
    public $COMFIRM_FIRE_ERROR_REQUEST = 'Error input';
    public $COMFIRM_FIRE_ERROR_NOT_FOUND = 'Not found in database';
    public $COMFIRM_FIRE_HANDLED = 'Fire handled';

    //get fire
    public $GET_FIRE_SUCCESSULLY = 'Get fire successfully';
    public $GET_FIRE_FAIL = 'Get fire fail';
    public $GET_FIRE_ERROR_REQUEST = 'Error input';
    public $GET_FIRE_ERROR_NOT_FOUND = 'Not found in database';

    //report fire handled
    public $REPORT_FIRE_HANDLED_SUCCESSFULLY = 'Report fire successfully';
    public $REPORT_FIRE_HANDLED_FAIL = 'Report fire fail';
    public $REPORT_FIRE_HANDLED_ERROR_REQUEST = 'Error input';
    public $REPORT_FIRE_HANDLED_ERROR_NOT_FOUND = 'Not found in database';
    public $REPORT_FIRE_HANDLED = 'Fire handled ago';

    //report fire handled
    public $EVALUATE_ACCURATE_FIRE_SUCCESSFULLY = 'Get result of evaluation accurate fire successfully';
    public $EVALUATE_ACCURATE_FIRE_FAIL = 'Get result of evaluation accurate fire fail';
    public $EVALUATE_ACCURATE_FIRE_ERROR_REQUEST = 'Error input';
    public $EVALUATE_ACCURATE_FIRE_ERROR_NOT_FOUND = 'Not found in database';

    //get fire local
    public $GET_FIRE_LOCAL_SUCCESSULLY = 'Get fire local successfully';
    public $GET_FIRE_LOCAL_FAIL = 'Get fire local fail';
    public $GET_FIRE_LOCAL_ERROR_REQUEST = 'Error input';
    public $GET_FIRE_LOCAL_ERROR_NOT_FOUND = 'Not found in database';

    //get fire local
    public $GET_NOTIFICATION_ACCIDENT_SUCCESSULLY = 'Get notification successfully';
    public $GET_NOTIFICATION_ACCIDENT_FAIL = 'Get notification fail';
    public $GET_NOTIFICATION_ACCIDENT_ERROR_REQUEST = 'Error input';
    public $GET_NOTIFICATION_ACCIDENT_ERROR_NOT_FOUND = 'Not found in database';
    public $GET_NOTIFICATION_ACCIDENT_ERROR_NOT_FOUND_TOKEN = 'Token error';

    //logout
    public $USER_LOGOUT_SUCCESSULLY = 'User logout successfully';
    public $USER_LOGOUT_FAIL = 'User logout fail';
    public $USER_LOGOUT_ERROR_REQUEST = 'Error input';
    public $USER_LOGOUT_ERROR_NOT_FOUND = 'Not found in database';

    //notification
    public $PUSH_NOTIFICATION_SUCCESSULLY = 'User logout successfully';
    public $PUSH_NOTIFICATION_FAIL = 'User logout fail';

}