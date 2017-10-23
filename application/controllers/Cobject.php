<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cobject extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('cobject_model');
    }

    // Function to redirect POST/GET action and set output
    public function index_api($strField = null) {
        $strMethod = $this->restapi->_detect_method();
        if ($strMethod === 'get') {
            $arrReturnData = $this->object_get($strField);
        }
        else {

            // Post call should not send key as GET call
            if(!empty($strField)){
                $arrReturnData = array('error_message' => 'Invalid request.');
            }
            else{
                $arrReturnData = $this->object_post();
            }

        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($arrReturnData));
    }

    // Function to handle GET action
    private function object_get($strField) {

        $arrValidQueryString = array('timestamp');
        $arrUserQueryString = $this->input->get();

        $arrInvalidQueryStrings = array();

        foreach($arrUserQueryString as $strKey => $strValue){
            if(!in_array($strKey, $arrValidQueryString)){
                $arrInvalidQueryStrings[] = $strKey;
            }
        }

        if(!empty($arrInvalidQueryStrings)){
            $arrData = array('error_message' => 'Invalid arguments: ' . implode(', ', $arrInvalidQueryStrings) . '.');
            return $arrData;
        }

        if (!empty($strField)) {

            //To get values based on timestamp
            $strTimeStamp = $this->input->get('timestamp');
            $arrData      = $this->cobject_model->getObjectData($strField, $strTimeStamp);

        }
        else {
            $arrData = array('error_message' => 'Invalid request.');
        }

        return $arrData;
    }

    // Function to handle POST action
    private function object_post() {

        $arrUserRequest = $this->input->post();
        $errMessage     = null;

        /** Validate required arguments */
        if (empty($arrUserRequest)) {
            $errMessage = 'Invalid Request';
        }
        else if (count($arrUserRequest) > 1) {
            $errMessage = 'Expected single array';
        }
        else {
            $arrData = $this->cobject_model->insertObjectData($arrUserRequest);
        }

        if (!empty($errMessage)) {
            return array('error_message' => $errMessage);
        }
        else {
            return $arrData;
        }
    }

    // Function to handle 404
    function index_404() {
        $this->output->set_content_type('application/json');
        $this->output->set_status_header('404');
        $arrOutput = array(
            'message' => 'URL Not Found',
            'status' => '404'
        );
        $this->output->set_output(json_encode($arrOutput));
    }

}
