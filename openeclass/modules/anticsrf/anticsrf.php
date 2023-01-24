<?

session_start();

// Same as filename_chiper() 
function random_chiper($filename){

    $ciphering = "AES-256-CBC";
    $iv_length = openssl_cipher_iv_length($ciphering);
    $ciphering_options = 0; // None
    $ciphering_iv = '1234567891011121';

    // Hashing a randrom password
    $ciphering_key = hash("sha256", sprintf('%08x', time()) . randomkeys(4) );

    // After the encryption we also md5-hash the encrypted filename to prevent weird characters in the filename
    return hash("md5",openssl_encrypt($filename,$ciphering, $ciphering_key, $ciphering_options, $ciphering_iv));
}


function store_in_session($key,$value){
	if (isset($_SESSION))
	{
		$_SESSION[$key]=$value;
	}
}

function unset_session($key){
	$_SESSION[$key]=' ';
	unset($_SESSION[$key]);
}

function get_from_session($key){
	if (isset($_SESSION[$key]))
	{
		return $_SESSION[$key];
	}
	else {  return false; }
}

function csrfguard_generate_token($unique_form_name){
	//$token = random_bytes(64); // PHP 7, or via paragonie/random_compat
    $token = random_chiper("H_php_einai_h_kaluterh_glwssa");
    store_in_session($unique_form_name,$token);
	return $token;
}

function hash_equals($str1, $str2) {
    if(strlen($str1) != strlen($str2)) {
      return false;
    } else {
      $res = $str1 ^ $str2;
      $ret = 0;
      for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
      return !$ret;
    }
}

function csrfguard_validate_token($unique_form_name,$token_value){
    $token = get_from_session($unique_form_name);
	if (!is_string($token_value)) {
        return false;
	}
	$result = hash_equals($token, $token_value);
	unset_session($unique_form_name);
	return $result;
}

function csrfguard_replace_forms($form_data_html){
	$count=preg_match_all("/<form(.*?)>(.*?)<\\/form>/is",$form_data_html,$matches,PREG_SET_ORDER);
	if (is_array($matches))
	{
		foreach ($matches as $m)
		{
			if (strpos($m[1],"nocsrf")!==false) { continue; }
			$name="CSRFGuard_".mt_rand(0,mt_getrandmax());
			$token=csrfguard_generate_token($name);
			$form_data_html=str_replace($m[0],
				"<form{$m[1]}>
                <input type='hidden' name='CSRFName' value='{$name}' />
                <input type='hidden' name='CSRFToken' value='{$token}' />{$m[2]}</form>",$form_data_html);
		}
	}
	return $form_data_html;
}

function csrfguard_inject(){
	$data=ob_get_clean();
	$data=csrfguard_replace_forms($data);
	echo $data;
}

function csrfguard_start(){
	if (count($_POST))
	{
		//echo 'CSRF check';
		if ( !isset($_POST['CSRFName']) or !isset($_POST['CSRFToken']) )
		{
			trigger_error("No CSRFName found, probable invalid request.",E_USER_ERROR);		
		} 
		$name =$_POST['CSRFName'];
		$token=$_POST['CSRFToken'];
		// echo $name;
		// echo $token;

		if (!csrfguard_validate_token($name, $token))
		{ 
			throw new Exception("Invalid CSRF token.");
		}
	}
	ob_start();
	/* adding double quotes for "csrfguard_inject" to prevent: 
          Notice: Use of undefined constant csrfguard_inject - assumed 'csrfguard_inject' */
	register_shutdown_function("csrfguard_inject");	
}

csrfguard_start();

//echo "anticsrf guard";

