<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Restapi {

    protected $CI;
    protected $allowed_http_methods = ['get', 'post'];

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function _detect_method() {
        // Declare a variable to store the method
        $strMethod = NULL;

        // Determine whether the 'enable_emulate_request' setting is enabled
        if ($this->CI->config->item('enable_emulate_request') === TRUE) {
            $strMethod = $this->CI->input->post('_method');
            if ($strMethod === NULL) {
                $strMethod = $this->CI->input->server('HTTP_X_HTTP_METHOD_OVERRIDE');
            }

            $strMethod = strtolower($strMethod);
        }

        if (empty($strMethod)) {
            // Get the request method as a lowercase string.
            $strMethod = $this->CI->input->method();
        }

        return in_array($strMethod, $this->allowed_http_methods) ? $strMethod : 'get';
    }

}
