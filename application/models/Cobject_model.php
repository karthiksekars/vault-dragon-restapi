<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cobject_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    //Function to create/update records based on key
    function insertObjectData($arrUserRequest = null) {

        //Parse and xss clean
        $arrParsedvalues = array(
            'ckey'         => $this->_xssClean(key($arrUserRequest)),
            'cvalue'       => $this->_xssClean(trim($arrUserRequest[key($arrUserRequest)])),
            'date_modified' => date('Y-m-d H:i:s')
        );

        $arrExistRecord = $this->getValueByKey($arrParsedvalues['ckey']);
        if (!empty($arrExistRecord)) {
            $this->db->where('ckey', $arrParsedvalues['ckey']);
            $this->db->update('api_objects', $arrParsedvalues);

            //For Audit Log
            if ($arrExistRecord['cvalue'] !== $arrParsedvalues['cvalue']) {
                $this->_updateObjectLog($arrParsedvalues['ckey'], $arrExistRecord['cvalue'], $arrParsedvalues['cvalue'], $arrExistRecord['date_modified']);
            }
        }
        else {
            $arrParsedvalues['date_created'] = date('Y-m-d H:i:s');
            $this->db->insert('api_objects', $arrParsedvalues);
        }

        //Retrieve latest value
        $this->db->flush_cache();
        $this->db->select('ckey as `key`, cvalue as `value`, UNIX_TIMESTAMP(date_modified) as timestamp');
        $this->db->where('ckey', trim($arrParsedvalues['ckey']));
        $this->db->from('api_objects');
        $objQuery = $this->db->get();
        $result   = $objQuery->row_array();
        return $result;
    }

    //Function to get latest value based on key
    function getValueByKey($strKey = null) {
        if (!empty($strKey)) {
            $this->db->select('id, cvalue, date_modified');
            $this->db->where('ckey', trim($strKey));
            $this->db->from('api_objects');
            $objQuery  = $this->db->get();
            $arrResult = $objQuery->row_array();
            if (!empty($arrResult)) {
                return $arrResult;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    //Function to get latest value based on key
    function getValueByTimestamp($strField = null, $strTimestamp = null) {
        if (!empty($strField) && !empty($strTimestamp)) {
            $this->db->select('ckey as `key`, old_value as `value`, UNIX_TIMESTAMP(date_created) as timestamp');
            $this->db->where('ckey', trim($strField));
            $this->db->where('UNIX_TIMESTAMP(date_created) <= ', trim($strTimestamp));
            $this->db->order_by('date_created', 'desc');
            $this->db->from('api_objects_audit_log');
            $query = $this->db->get();
            $arrResult = $query->row_array();
            if(empty($arrResult)){
                $this->db->select('ckey as `key`, cvalue as `value`, UNIX_TIMESTAMP(date_modified) as timestamp');
                $this->db->where('ckey', trim($strField));
                $this->db->where('UNIX_TIMESTAMP(date_modified) <= ', trim($strTimestamp));
                $this->db->order_by('date_created', 'desc');
                $this->db->from('api_objects');
                $query = $this->db->get();
                $arrResult = $query->row_array();
                return $arrResult;
            }
            else{
                return $arrResult;
            }
        }
        else {
            return false;
        }
    }

    //Function to return value based or key and/or timestamp
    function getObjectData($strField = null, $strTimeStamp = null) {
        $this->db->flush_cache();

        if (!empty($strTimeStamp)) {
            $arrResult = $this->getValueByTimestamp($strField, $strTimeStamp);
        }
        else {
            $this->db->select('cvalue as `value`');
            $this->db->where('ckey', trim($strField));
            $this->db->from('api_objects');
            $query = $this->db->get();
            $arrResult = $query->row_array();
        }

        if (!empty($arrResult)) {
            return $arrResult;
        }
        else {
            return array('error_message' => 'Record could not be found');
        }
    }

    //Function to update Audit log to keep history
    protected function _updateObjectLog($strKey = null, $strOldValue = null, $strNewValue = null) {

        $arrLogValues = array(
            'ckey'      => $strKey,
            'old_value' => $strOldValue,
            'new_value' => $strNewValue
        );
        $this->db->insert('api_objects_audit_log', $arrLogValues);
    }

    //Function to prevent xss attack
    protected function _xssClean($value) {
        return $this->security->xss_clean($value);
    }

}
