<?php 


function var_request($key, $default) {
	$value = $default;
	if(isset($_GET[$key])) { 
		$value = $_GET[$key];
	}
	if(isset($_POST[$key])) { 
		$value = $_POST[$key];
	} 
	if($value=="") 
		$value=$default;
	return $value;
}
function isNotEmpty($value) {
    if (is_array($value)) {
        if (count($value) > 0) {
            return true;
        }
        else {
            return false;
        }
    }
    else if (!is_null($value)) {
        $value = trim($value);
        if ($value === '') {
            return false;
        }
        else {
            return true;
        }
    }
    else {
        return false;
    }
}

function tao_var_get($var_key, $var_default = null) {
    if (function_exists($var_key . '_get')) {
        $value = call_user_func($var_key . '_get');

        if (null != $value) {
            return $value;
        }
    }

    $var_key =  $var_key;
    $value = get_option($var_key, $var_default);

    return $value;
}

function tao_var_set($var_key, $var_value) {
    if (function_exists($var_key . '_set')) {
        return call_user_func($var_key . '_set', $var_value);
    }

    $value = tao_var_get($var_key, null);

    if (null !== $value) {
        update_option( $var_key, $var_value);
    }
    else if ('' === $value) {
        update_option( $var_key, $var_value);
    }
    else {
        add_option( $var_key, $var_value);
    }
}

function tao_var_delete($var_key) {
    delete_option( $var_key);
}
?>