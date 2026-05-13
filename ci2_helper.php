<?php

// For get single record from DB
function get_single_row_helper($table, $select = "*", $where = "", $order_by = [])
{
    $CI =& get_instance();

    $CI->db->select($select);	
    $CI->db->from($table);
    
    if ($where != "")
        $CI->db->where($where);

    if (!empty($order_by)) {
        $CI->db->order_by($order_by[0], $order_by[1]);
    }

    $CI->db->limit(1);
    
    $query = $CI->db->get();
    if ($query === FALSE) {
        return [];
    } else {
        $res = $query->result_array();
    }
    
    
    return (!empty($res) ? $res[0] : $res);
}

// For get list record from DB
function get_single_row_helper_parameterized($table, $select = "*", $where = [], $order_by = [])
{
    $CI = &get_instance();
    $db = $CI->load->database('default', TRUE);


    $allowed_identifier = '/^[a-zA-Z0-9_\.\,\s\*]+$/';

    if (!preg_match($allowed_identifier, $table)) {
        echo 'get_list_helper_parameterized: invalid table name: ' . $table;
        return [];
    }

    if ($select !== '*' && !preg_match($allowed_identifier, $select)) {
        echo 'get_list_helper_parameterized: invalid select: ' . $select;
        return [];
    }

    $sql = "SELECT $select FROM $table";

    // $db->select($select, FALSE);
    // $db->from($table);

    if (is_array($where) && !empty($where) && isset($where[0]) && isset($where[1])) {
        // $db->where($where[0], NULL, FALSE);
        $sql .= " WHERE (". $where[0].")";
    }

    if (!empty($order_by) && isset($order_by[0]) && isset($order_by[1])) {
        $allowed_directions = ['ASC', 'DESC'];
        $direction = strtoupper($order_by[1]);

        if (!preg_match($allowed_identifier, $order_by[0]) || !in_array($direction, $allowed_directions)) {
            echo  'get_list_helper_parameterized: invalid order_by';
            echo $sql."<br>";

            return [];
        }

        // Safty check column name with reserved sql keyword
        $reserved_keywords = ['index', 'order', 'group', 'by', 'limit', 'offset', 'join', 'inner', 'left', 'right', 'on'];
        $order_by[0] = (in_array(strtolower($order_by[0]), $reserved_keywords) ? "`".$order_by[0]."`" : $order_by[0]); 
        $sql .= " ORDER BY ".$order_by[0]." ".$order_by[1];

    }

    $query = $db->query($sql, $where[1]);

    if ($query === FALSE) {
        return [];
    }
    return $query->row_array();
}

// For get list record from DB
function get_list_helper($table, $select = "*", $where = "", $order_by = [], $group_by = "", $limit = "")
{
    $CI = &get_instance();
    //  IMPORTANT: get a FRESH DB INSTANCE
    $db = $CI->load->database('default', TRUE);
    $db->select($select, false);
    $db->from($table);
    if ($where != "" || !empty($where))
        $db->where($where);
    if (!empty($order_by))
        $db->order_by($order_by[0], $order_by[1]);
    if (!empty($group_by) || $group_by != "")
        $db->group_by($group_by);
    if (!empty($limit)) {
        $db->limit($limit);
    }
    $query = $db->get();
    if ($query === FALSE) {
        return [];
    } else {
        $res = $query->result_array();
    }
    return $res;
}

// For get list record from DB
function get_list_helper_parameterized($table, $select = "*", $where = "", $order_by = [], $group_by = "", $limit = "")
{
    $CI = &get_instance();
    $db = $CI->load->database('default', TRUE);

    $allowed_identifier = '/^[a-zA-Z0-9_\.\,\s\*]+$/';

    if (!preg_match($allowed_identifier, $table)) {
        log_message('error', 'get_list_helper_parameterized: invalid table name: ' . $table);
        return [];
    }

    if ($select !== '*' && !preg_match($allowed_identifier, $select)) {
        log_message('error', 'get_list_helper_parameterized: invalid select: ' . $select);
        return [];
    }

    $db->select($select, FALSE);
    $db->from($table);

    if (is_array($where) && !empty($where) && isset($where[0]) && isset($where[1])) {
        $db->where($where[0], NULL, FALSE);
    }

    if (!empty($group_by)) {
        if (!preg_match($allowed_identifier, $group_by)) {
            log_message('error', 'get_list_helper_parameterized: invalid group_by: ' . $group_by);
            return [];
        }
        $db->group_by($group_by);
    }

    if (!empty($order_by) && isset($order_by[0]) && isset($order_by[1])) {
        $allowed_directions = ['ASC', 'DESC'];
        $direction = strtoupper($order_by[1]);

        if (!preg_match($allowed_identifier, $order_by[0]) || !in_array($direction, $allowed_directions)) {
            log_message('error', 'get_list_helper_parameterized: invalid order_by');
            return [];
        }
        $db->order_by($order_by[0], $direction);
    }

    if ($limit !== "") {
        $db->limit((int)$limit);
    }

    $query = $db->get();

    if ($query === FALSE) {
        return [];
    }

    return $query->result_array();
}

// For insert 
function db_insert_helper($table, $data, $last_insert_id = false){
    $CI =& get_instance();

    $res =  $CI->db->insert($table, $data);
    if($last_insert_id && $res)
        return $CI->db->insert_id();
    return $res;
}

// For insert batch
function db_insert_batch_helper($table, $data){
    $CI =& get_instance();

    $res =  $CI->db->insert_batch($table, $data);
    return $res;
}

// For insert batch
function db_update_helper($table, $data, $where=''){
    $CI =& get_instance();

    if($where != "")
        $CI->db->where($where);  

    $res = $CI->db->update($table, $data); 
    return $CI->db->affected_rows();
}

function db_update_helper_where_array($table, $data, $where = [])
{
    $CI =& get_instance();

    if (!empty($where)) {
        $CI->db->where($where);  // safe
    }

    $CI->db->update($table, $data);
    return $CI->db->affected_rows();
}

function db_batch_update_helper($table, $data, $where_key){
    $CI =& get_instance();

    $res =  $CI->db->update_batch($table, $data, $where_key); 
    return ($res > 0) ? $res : 0; // return number of rows updated, or 0 if none updated
}

function get_dropdown_helper($table, $value_field = "", $name_field = "", $first_null_val = "", $where = "", $order_by = [], $group_by = '')
{

    $CI =& get_instance();
	$name_field_key = $name_field;
	if (is_array($name_field)) {
		$name_field_key = $name_field[1];
		$name_field = $name_field[0]. 'as '.$name_field_key;
	}

	$result = get_list_helper($table, $value_field . ',' . $name_field, $where, $order_by, $group_by);

	$dropdown_data = [];
	if ($first_null_val != "")
		$dropdown_data[""] = $first_null_val;

	foreach ($result as $row) {
		$dropdown_data[$row[$value_field]] = $row[$name_field_key];
	}
	return $dropdown_data;
}


function get_db_fields($table_name){

    $CI =& get_instance();
    $field_list = $CI->db->field_data($table_name); 
    return array_map(function($row){ return $row->name;}, $field_list);
}

function open_ai_spellchecker($word='car'){

    $apiKey = 'sk-proj-vdgfhjkdasfjhdafjsdghjfdfuiemnvirengb843hrb783v728hr8gtv37bfh3gb';

    // Define the API endpoint and payload
    $url = 'https://api.openai.com/v1/chat/completions';
    $data = [
        'model' => 'gpt-4o',
        'messages' => [
            ['role' => 'system', 'content' => 'Pls corrects spelling and grammar mistakes related to singular cars names. Output language same as input'],
            ['role' => 'user', 'content' => 'The corrected output must be empty or a name of vehicle brand, model or UAE cities or combination of all stright answer only: "' . $word . '"']
        ],
        'max_tokens' => 100,
        'temperature' => 0
    ];

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . 'Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $correctedText = false;
    if (curl_errno($ch) || $httpCode !== 200) {
        return false;
    } else {
        $responseData = json_decode($response, true);
        $correctedText = $responseData['choices'][0]['message']['content'];
    }

    // Close cURL
    curl_close($ch);

    // Check for word correction
    $word = keyword_correction($correctedText);
    return $word;
}

function keyword_correction($word){

    $mapping_data = [
        'Jettour' => "Jetour",
        'Jetur' => "Jetour",
        'Huracán' => "Huracan",
        'Lamborghini Huracán' => "Lamborghini Huracan",
        'None' => "Van",
        'Petrol' => "Patrol",
    ];

    return (isset($mapping_data[$word])?$mapping_data[$word]:$word);
}

// Get the wa number for the listing id as per the configuration 
// Type(also Cache-key): wa_config_switch_rental or wa_config_switch_sale
function wa_get_receiving_number($whatsapp_number, $client_config, $type = 'wa_config_switch_sale', $country = "UAE"){
 
    // Whatsapp API Disabled clients
    if($client_config == '-1'){
        return $whatsapp_number;
    }
    $CI =& get_instance();

    // Check for the global config
    $CI->load->library('memcached_library');

    // Get the OCD WA number for validate
    $wa_number_list = json_decode(WHATSAPP_OCD_NUMBERS, true);
    
    // For memcache key (Multiple num)
    $country_1 = strtolower(str_replace(' ','-', trim($country))); // Eg: Saudi Arabia => saudi-arabia
    $key = ((isset($wa_number_list[$country]) && $country !="UAE")? $type.'_'.$country_1 :$type);

    $global_wa_config = $CI->memcached_library->get($key);

    // If no cache value available then create new cache val with the key
    if($global_wa_config == '') {

        $config_check = ['config_type' => 'api_switch', 'group' => 'default'];

        if(isset($wa_number_list[$country]) && $country !="UAE"){
            $config_check['group'] = $country_1;
        }

        $wa_global_config = get_single_row_helper('whatsapp_api_configs', '*', $config_check);

        $global_wa_config = ($type == 'wa_config_switch_sale')?$wa_global_config['usedcar_value']:$wa_global_config['rental_value'];
        $CI->memcached_library->set($key, $global_wa_config);

    }
    
    if($global_wa_config == 'on' || ($client_config == '1' && $global_wa_config != 'off')){
        // Get the all numbers
        $whatsapp_number = (isset($wa_number_list[$country]))?$wa_number_list[$country]: WHATSAPP_PHONE_NUMBER;

    }  
    return $whatsapp_number;
}

function customUcwords($string) {
    $lowercaseWords = ['by', 'and', 'or', 'for', 'of', 'the', 'to', 'a', 'in'];
    $preserveUpper = ['LLC', 'LLC.', 'L.L.C'];

    $words = explode(' ', $string);

    foreach ($words as $key => $word) {
        $wordLower = strtolower($word);

        if (in_array($word, $preserveUpper)) {
            $words[$key] = strtoupper($word);
        } elseif (in_array($wordLower, $lowercaseWords) && $key !== 0) {
            $words[$key] = $wordLower;
        } else {
            $words[$key] = ucfirst($wordLower);
        }
    }

    return implode(' ', $words);
}


/**
    * Function for update elastic 
    * 
    * @param $index_for: Eg rental & used_car
    * @param $data: array of fields to be updated Eg ['name' => 'Ferrari', 'daily_price' => '2000']
    * @param $conditions:  array of fields to check Eg: ['id' => '1245']

    * @return: Number of updated docs if success or False 
*/ 
function elastic_update_helper($index_for, $data=[], $conditions){
    require_once(APPPATH.'elastic_php/vendor/autoload.php');

    // Check for index name
    if($index_for == 'rental')
        $index = 'oneclick_test';
    else if($index_for == 'used_car')
        $index = 'oneclick_usedcar';
    else
        $index = $index_for;
        
    // Check for the data is empty
    if(empty($data) || empty($conditions)){
        return false;
        echo "Data Array or Condition array invalid!";
    }

    // Condition must be include id or client id
    if(!(isset($conditions['id']) || isset($conditions['client_id']))){
        echo "Condition required to filter by Id or Lisitng ID";
        return false;
    }
    
    // Build "must" conditions dynamically
    $must = [];
    foreach ($conditions as $field => $value) {
        $must[] = [
            'term' => [
                $field => $value
            ]
        ];
    }

    $params = [
        'index' => $index, // Index
        'body'  => [
            'script' => [
                'source' => '
                    for (entry in params.fields.entrySet()) {
                        ctx._source[entry.getKey()] = entry.getValue();
                    }
                ',
                'lang'   => 'painless',
                'params' => [
                    'fields' => $data
                ]
            ],
            'query' => [
                'bool' => [
                    'must' => $must   // Apply all conditions (AND)
                ]
            ]
        ]
    ];

    try{
        $client4 = \Elasticsearch\ClientBuilder::create()->build();
        $res_es = $client4->updateByQuery($params);
    }catch(Exception $e){
        echo $e->getMessage();
        return false;
    }

    return isset($res_es['updated'])?$res_es['updated']:false;

}


function ocd_clean_phone_number($countryCode, $nationalNumber) {
    // 1. Remove all non-digit characters from national number
    $nationalNumber = preg_replace('/\D/', '', $nationalNumber);

    // 2. Remove leading 0 if present (trunk code)
    $nationalNumber = ltrim($nationalNumber, '0');

    // 3. Ensure country code starts with '+'
    $countryCode = preg_replace('/\D/', '', $countryCode); // remove any non-digits
    if (strpos($countryCode, '+') !== 0) {
        $countryCode = '+' . $countryCode;
    }

    // 4. Combine
    $fullNumber = $countryCode . $nationalNumber;

    // 5. Simple validation: must be 10-15 digits in total (common E.164 range)
    if (!preg_match('/^\+\d{10,15}$/', $fullNumber)) {
        return false; // invalid
    }

    return $fullNumber;
}
function es_update_usedcar_elastic_bulk($index_for,$car_list)
{
    require_once(APPPATH.'elastic_php/vendor/autoload.php');
    $bulkParams = ['body' => []];

    //$car_list = get_list_helper('usedcar_listings', "id, phone_number, whatsapp_number, client_id", "client_id = 7645 ");

    //$client_info = get_single_row_helper('clients', "*","client_id = '7645'");
    // $clien

    if($index_for == 'rental')
        $index = 'oneclick_test';
    else if($index_for == 'used_car')
        $index = 'usedcar';

    foreach($car_list as $car){
        $bulkParams['body'][] = [
            'update' => [
                '_index' => $index,
                '_id'    => $car['id']
            ]
        ];
        $bulkParams['body'][] = [
            'doc' => [
                'whatsapp_number' => $car['whatsapp_number'],
                'phone_number' =>  $car['phone_number']
            ]
        ];

    }
    
    // echo "<pre>";print_r($bulkParams); die;
    try{
        $client4 = \Elasticsearch\ClientBuilder::create()->build();
        $response = $client4->bulk($bulkParams);
    }catch(Exception $e){
        echo $e->getMessage();
        return false;
    }

    //echo "<pre>";print_r($response); 
    //die;
}


function ocd_number_to_text($number) {
  	ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
  
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary=[0=>'zero',1=>'one',2=>'two',3=>'three',4=>'four',5=>'five',6=>'six',7=>'seven',8=>'eight',9=>'nine',10=>'ten',11=>'eleven',12=>'twelve',13=>'thirteen',14=>'fourteen',15=>'fifteen',16=>'sixteen',17=>'seventeen',18=>'eighteen',19=>'nineteen',20=>'twenty',30=>'thirty',40=>'forty',50=>'fifty',60=>'sixty',70=>'seventy',80=>'eighty',90=>'ninety',100=>'hundred',1000=>'thousand',1000000=>'million',1000000000=>'billion',1000000000000=>'trillion',1000000000000000=>'quadrillion',1000000000000000000=>'quintillion'];

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'ocd_number_to_text only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . ocd_number_to_text(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int)($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = (int)($number / 100);
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . ocd_number_to_text($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = ocd_number_to_text($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= ocd_number_to_text($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = [];
        foreach (str_split((string)$fraction) as $digit) {
            $words[] = $dictionary[$digit];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function sendsms_all($number,$messagebody){	
    require_once(APPPATH.'controllers/vendor/autoload.php');

    // Configure HTTP basic authorization: BasicAuth
       
    $config = ClickSend\Configuration::getDefaultConfiguration()
    ->setUsername('moahemd@ocd.com')
    ->setPassword('HBGSHJD-8B8FEB4D-8B8FEB4D8B8FEB4D0194-KJNSDBYVT');


    $apiInstance = new ClickSend\Api\SMSApi(new GuzzleHttp\Client(),$config);
    $msg = new \ClickSend\Model\SmsMessage();
    $msg->setBody($messagebody); 
    $msg->setTo("$number");
    $msg->setSource("sdk");
    
    // \ClickSend\Model\SmsMessageCollection | SmsMessageCollection model
    $sms_messages = new \ClickSend\Model\SmsMessageCollection(); 
    $sms_messages->setMessages([$msg]);
    
    try {
        $apiInstance->smsSendPost($sms_messages);
        //print_r($result);
        return "success";
        
    } catch (Exception $e) {
    // echo 'Exception when calling SMSApi->smsSendPost: ', $e->getMessage(), PHP_EOL;
    }
}

function TimeAgo($timestamp) {
    if (!is_numeric($timestamp)) {
        $timestamp = strtotime($timestamp);
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return "just now";
    }

    if ($diff < 3600) { 
        $mins = floor($diff / 60);
        return $mins . " min." . " ago";
    }

    if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    }

    if ($diff < 172800) { // < 2 days
        return "yesterday";
    }

    if ($diff < 604800) { // < 7 days
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    }

    if ($diff < 2592000) { // < 30 days
        $weeks = floor($diff / 604800);
        return $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
    }

    if ($diff < 31536000) { // < 1 year
        $months = floor($diff / 2592000);
        return $months . " month" . ($months > 1 ? "s" : "") . " ago";
    }

    $years = floor($diff / 31536000);
    return $years . " year" . ($years > 1 ? "s" : "") . " ago";
}



function get_country_from_phone_number($phoneNumber) {
    // Remove non-numeric characters except '+'
    $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

    // Extract the country code (1 to 4 digits after '+')
    if (preg_match('/^\+?(\d{1,3})/', $phoneNumber, $matches)) {
        $countryCode = $matches[1];
        
        // Country code to country name mapping
        $country_data = '{"93":["Afghanistan","\ud83c\udde6\ud83c\uddeb"],"358":["Finland","\ud83c\uddeb\ud83c\uddee"],"355":["Albania","\ud83c\udde6\ud83c\uddf1"],"213":["Algeria","\ud83c\udde9\ud83c\uddff"],"1684":["AmericanSamoa","\ud83c\udde6\ud83c\uddf8"],"376":["Andorra","\ud83c\udde6\ud83c\udde9"],"244":["Angola","\ud83c\udde6\ud83c\uddf4"],"1264":["Anguilla","\ud83c\udde6\ud83c\uddee"],"672":["Norfolk Island","\ud83c\uddf3\ud83c\uddeb"],"1268":["Antigua and Barbuda","\ud83c\udde6\ud83c\uddec"],"54":["Argentina","\ud83c\udde6\ud83c\uddf7"],"374":["Armenia","\ud83c\udde6\ud83c\uddf2"],"297":["Aruba","\ud83c\udde6\ud83c\uddfc"],"61":["Cocos (Keeling) Islands","\ud83c\udde8\ud83c\udde8"],"43":["Austria","\ud83c\udde6\ud83c\uddf9"],"994":["Azerbaijan","\ud83c\udde6\ud83c\uddff"],"1242":["Bahamas","\ud83c\udde7\ud83c\uddf8"],"973":["Bahrain","\ud83c\udde7\ud83c\udded"],"880":["Bangladesh","\ud83c\udde7\ud83c\udde9"],"1246":["Barbados","\ud83c\udde7\ud83c\udde7"],"375":["Belarus","\ud83c\udde7\ud83c\uddfe"],"32":["Belgium","\ud83c\udde7\ud83c\uddea"],"501":["Belize","\ud83c\udde7\ud83c\uddff"],"229":["Benin","\ud83c\udde7\ud83c\uddef"],"1441":["Bermuda","\ud83c\udde7\ud83c\uddf2"],"975":["Bhutan","\ud83c\udde7\ud83c\uddf9"],"591":["Bolivia, Plurinational State of","\ud83c\udde7\ud83c\uddf4"],"387":["Bosnia and Herzegovina","\ud83c\udde7\ud83c\udde6"],"267":["Botswana","\ud83c\udde7\ud83c\uddfc"],"55":["Brazil","\ud83c\udde7\ud83c\uddf7"],"246":["British Indian Ocean Territory","\ud83c\uddee\ud83c\uddf4"],"673":["Brunei Darussalam","\ud83c\udde7\ud83c\uddf3"],"359":["Bulgaria","\ud83c\udde7\ud83c\uddec"],"226":["Burkina Faso","\ud83c\udde7\ud83c\uddeb"],"257":["Burundi","\ud83c\udde7\ud83c\uddee"],"855":["Cambodia","\ud83c\uddf0\ud83c\udded"],"237":["Cameroon","\ud83c\udde8\ud83c\uddf2"],"1":["United States","\ud83c\uddfa\ud83c\uddf8"],"238":["Cape Verde","\ud83c\udde8\ud83c\uddfb"],"345":["Cayman Islands","\ud83c\uddf0\ud83c\uddfe"],"236":["Central African Republic","\ud83c\udde8\ud83c\uddeb"],"235":["Chad","\ud83c\uddf9\ud83c\udde9"],"56":["Chile","\ud83c\udde8\ud83c\uddf1"],"86":["China","\ud83c\udde8\ud83c\uddf3"],"57":["Colombia","\ud83c\udde8\ud83c\uddf4"],"269":["Comoros","\ud83c\uddf0\ud83c\uddf2"],"242":["Congo","\ud83c\udde8\ud83c\uddec"],"243":["Congo, The Democratic Republic of the Congo","\ud83c\udde8\ud83c\udde9"],"682":["Cook Islands","\ud83c\udde8\ud83c\uddf0"],"506":["Costa Rica","\ud83c\udde8\ud83c\uddf7"],"225":["Cote d`Ivoire","\ud83c\udde8\ud83c\uddee"],"385":["Croatia","\ud83c\udded\ud83c\uddf7"],"53":["Cuba","\ud83c\udde8\ud83c\uddfa"],"357":["Cyprus","\ud83c\udde8\ud83c\uddfe"],"420":["Czech Republic","\ud83c\udde8\ud83c\uddff"],"45":["Denmark","\ud83c\udde9\ud83c\uddf0"],"253":["Djibouti","\ud83c\udde9\ud83c\uddef"],"1767":["Dominica","\ud83c\udde9\ud83c\uddf2"],"1849":["Dominican Republic","\ud83c\udde9\ud83c\uddf4"],"593":["Ecuador","\ud83c\uddea\ud83c\udde8"],"20":["Egypt","\ud83c\uddea\ud83c\uddec"],"503":["El Salvador","\ud83c\uddf8\ud83c\uddfb"],"240":["Equatorial Guinea","\ud83c\uddec\ud83c\uddf6"],"291":["Eritrea","\ud83c\uddea\ud83c\uddf7"],"372":["Estonia","\ud83c\uddea\ud83c\uddea"],"251":["Ethiopia","\ud83c\uddea\ud83c\uddf9"],"500":["South Georgia and the South Sandwich Islands","\ud83c\uddec\ud83c\uddf8"],"298":["Faroe Islands","\ud83c\uddeb\ud83c\uddf4"],"679":["Fiji","\ud83c\uddeb\ud83c\uddef"],"33":["France","\ud83c\uddeb\ud83c\uddf7"],"594":["French Guiana","\ud83c\uddec\ud83c\uddeb"],"689":["French Polynesia","\ud83c\uddf5\ud83c\uddeb"],"241":["Gabon","\ud83c\uddec\ud83c\udde6"],"220":["Gambia","\ud83c\uddec\ud83c\uddf2"],"995":["Georgia","\ud83c\uddec\ud83c\uddea"],"49":["Germany","\ud83c\udde9\ud83c\uddea"],"233":["Ghana","\ud83c\uddec\ud83c\udded"],"350":["Gibraltar","\ud83c\uddec\ud83c\uddee"],"30":["Greece","\ud83c\uddec\ud83c\uddf7"],"299":["Greenland","\ud83c\uddec\ud83c\uddf1"],"1473":["Grenada","\ud83c\uddec\ud83c\udde9"],"590":["Saint Martin","\ud83c\uddf2\ud83c\uddeb"],"1671":["Guam","\ud83c\uddec\ud83c\uddfa"],"502":["Guatemala","\ud83c\uddec\ud83c\uddf9"],"44":["United Kingdom","\ud83c\uddec\ud83c\udde7"],"224":["Guinea","\ud83c\uddec\ud83c\uddf3"],"245":["Guinea-Bissau","\ud83c\uddec\ud83c\uddfc"],"595":["Paraguay","\ud83c\uddf5\ud83c\uddfe"],"509":["Haiti","\ud83c\udded\ud83c\uddf9"],"379":["Holy See (Vatican City State)","\ud83c\uddfb\ud83c\udde6"],"504":["Honduras","\ud83c\udded\ud83c\uddf3"],"852":["Hong Kong","\ud83c\udded\ud83c\uddf0"],"36":["Hungary","\ud83c\udded\ud83c\uddfa"],"354":["Iceland","\ud83c\uddee\ud83c\uddf8"],"91":["India","\ud83c\uddee\ud83c\uddf3"],"62":["Indonesia","\ud83c\uddee\ud83c\udde9"],"98":["Iran, Islamic Republic of Persian Gulf","\ud83c\uddee\ud83c\uddf7"],"964":["Iraq","\ud83c\uddee\ud83c\uddf7"],"353":["Ireland","\ud83c\uddee\ud83c\uddea"],"972":["Israel","\ud83c\uddee\ud83c\uddf1"],"39":["Italy","\ud83c\uddee\ud83c\uddf9"],"1876":["Jamaica","\ud83c\uddef\ud83c\uddf2"],"81":["Japan","\ud83c\uddef\ud83c\uddf5"],"962":["Jordan","\ud83c\uddef\ud83c\uddf4"],"77":["Kazakhstan","\ud83c\uddf0\ud83c\uddff"],"254":["Kenya","\ud83c\uddf0\ud83c\uddea"],"686":["Kiribati","\ud83c\uddf0\ud83c\uddee"],"850":["Korea, Democratic People`s Republic of Korea","\ud83c\uddf0\ud83c\uddf5"],"82":["Korea, Republic of South Korea","\ud83c\uddf0\ud83c\uddf7"],"965":["Kuwait","\ud83c\uddf0\ud83c\uddfc"],"996":["Kyrgyzstan","\ud83c\uddf0\ud83c\uddec"],"856":["Laos","\ud83c\uddf1\ud83c\udde6"],"371":["Latvia","\ud83c\uddf1\ud83c\uddfb"],"961":["Lebanon","\ud83c\uddf1\ud83c\udde7"],"266":["Lesotho","\ud83c\uddf1\ud83c\uddf8"],"231":["Liberia","\ud83c\uddf1\ud83c\uddf7"],"218":["Libyan Arab Jamahiriya","\ud83c\uddf1\ud83c\uddfe"],"423":["Liechtenstein","\ud83c\uddf1\ud83c\uddee"],"370":["Lithuania","\ud83c\uddf1\ud83c\uddf9"],"352":["Luxembourg","\ud83c\uddf1\ud83c\uddfa"],"853":["Macao","\ud83c\uddf2\ud83c\uddf4"],"389":["Macedonia","\ud83c\uddf2\ud83c\uddf0"],"261":["Madagascar","\ud83c\uddf2\ud83c\uddec"],"265":["Malawi","\ud83c\uddf2\ud83c\uddfc"],"60":["Malaysia","\ud83c\uddf2\ud83c\uddfe"],"960":["Maldives","\ud83c\uddf2\ud83c\uddfb"],"223":["Mali","\ud83c\uddf2\ud83c\uddf1"],"356":["Malta","\ud83c\uddf2\ud83c\uddf9"],"692":["Marshall Islands","\ud83c\uddf2\ud83c\udded"],"596":["Martinique","\ud83c\uddf2\ud83c\uddf6"],"222":["Mauritania","\ud83c\uddf2\ud83c\uddf7"],"230":["Mauritius","\ud83c\uddf2\ud83c\uddfa"],"262":["Reunion","\ud83c\uddf7\ud83c\uddea"],"52":["Mexico","\ud83c\uddf2\ud83c\uddfd"],"691":["Micronesia, Federated States of Micronesia","\ud83c\uddeb\ud83c\uddf2"],"373":["Moldova","\ud83c\uddf2\ud83c\udde9"],"377":["Monaco","\ud83c\uddf2\ud83c\udde8"],"976":["Mongolia","\ud83c\uddf2\ud83c\uddf3"],"382":["Montenegro","\ud83c\uddf2\ud83c\uddea"],"1664":["Montserrat","\ud83c\uddf2\ud83c\uddf8"],"212":["Morocco","\ud83c\uddf2\ud83c\udde6"],"258":["Mozambique","\ud83c\uddf2\ud83c\uddff"],"95":["Myanmar","\ud83c\uddf2\ud83c\uddf2"],"264":["Namibia","\ud83c\uddf3\ud83c\udde6"],"674":["Nauru","\ud83c\uddf3\ud83c\uddf7"],"977":["Nepal","\ud83c\uddf3\ud83c\uddf5"],"31":["Netherlands","\ud83c\uddf3\ud83c\uddf1"],"599":["Netherlands Antilles","\ud83c\udde7\ud83c\uddf6"],"687":["New Caledonia","\ud83c\uddf3\ud83c\udde8"],"64":["New Zealand","\ud83c\uddf3\ud83c\uddff"],"505":["Nicaragua","\ud83c\uddf3\ud83c\uddee"],"227":["Niger","\ud83c\uddf3\ud83c\uddea"],"234":["Nigeria","\ud83c\uddf3\ud83c\uddec"],"683":["Niue","\ud83c\uddf3\ud83c\uddfa"],"1670":["Northern Mariana Islands","\ud83c\uddf2\ud83c\uddf5"],"47":["Svalbard and Jan Mayen","\ud83c\uddf8\ud83c\uddef"],"968":["Oman","\ud83c\uddf4\ud83c\uddf2"],"92":["Pakistan","\ud83c\uddf5\ud83c\uddf0"],"680":["Palau","\ud83c\uddf5\ud83c\uddfc"],"970":["Palestinian Territory, Occupied","\ud83c\uddf5\ud83c\uddf8"],"507":["Panama","\ud83c\uddf5\ud83c\udde6"],"675":["Papua New Guinea","\ud83c\uddf5\ud83c\uddec"],"51":["Peru","\ud83c\uddf5\ud83c\uddea"],"63":["Philippines","\ud83c\uddf5\ud83c\udded"],"872":["Pitcairn","\ud83c\uddf5\ud83c\uddf3"],"48":["Poland","\ud83c\uddf5\ud83c\uddf1"],"351":["Portugal","\ud83c\uddf5\ud83c\uddf9"],"1939":["Puerto Rico","\ud83c\uddf5\ud83c\uddf7"],"974":["Qatar","\ud83c\uddf6\ud83c\udde6"],"40":["Romania","\ud83c\uddf7\ud83c\uddf4"],"7":["Russia","\ud83c\uddf7\ud83c\uddfa"],"250":["Rwanda","\ud83c\uddf7\ud83c\uddfc"],"290":["Saint Helena, Ascension and Tristan Da Cunha","\ud83c\uddf8\ud83c\udded"],"1869":["Saint Kitts and Nevis","\ud83c\uddf0\ud83c\uddf3"],"1758":["Saint Lucia","\ud83c\uddf1\ud83c\udde8"],"508":["Saint Pierre and Miquelon","\ud83c\uddf5\ud83c\uddf2"],"1784":["Saint Vincent and the Grenadines","\ud83c\uddfb\ud83c\udde8"],"685":["Samoa","\ud83c\uddfc\ud83c\uddf8"],"378":["San Marino","\ud83c\uddf8\ud83c\uddf2"],"239":["Sao Tome and Principe","\ud83c\uddf8\ud83c\uddf9"],"966":["Saudi Arabia","\ud83c\uddf8\ud83c\udde6"],"221":["Senegal","\ud83c\uddf8\ud83c\uddf3"],"381":["Serbia","\ud83c\uddf7\ud83c\uddf8"],"248":["Seychelles","\ud83c\uddf8\ud83c\udde8"],"232":["Sierra Leone","\ud83c\uddf8\ud83c\uddf1"],"65":["Singapore","\ud83c\uddf8\ud83c\uddec"],"421":["Slovakia","\ud83c\uddf8\ud83c\uddf0"],"386":["Slovenia","\ud83c\uddf8\ud83c\uddee"],"677":["Solomon Islands","\ud83c\uddf8\ud83c\udde7"],"252":["Somalia","\ud83c\uddf8\ud83c\uddf4"],"27":["South Africa","\ud83c\uddff\ud83c\udde6"],"211":["South Sudan","\ud83c\uddf8\ud83c\uddf8"],"34":["Spain","\ud83c\uddea\ud83c\uddf8"],"94":["Sri Lanka","\ud83c\uddf1\ud83c\uddf0"],"249":["Sudan","\ud83c\uddf8\ud83c\udde9"],"597":["Suriname","\ud83c\uddf8\ud83c\uddf7"],"268":["Swaziland","\ud83c\uddf8\ud83c\uddff"],"46":["Sweden","\ud83c\uddf8\ud83c\uddea"],"41":["Switzerland","\ud83c\udde8\ud83c\udded"],"963":["Syrian Arab Republic","\ud83c\uddf8\ud83c\uddfe"],"886":["Taiwan","\ud83c\uddf9\ud83c\uddfc"],"992":["Tajikistan","\ud83c\uddf9\ud83c\uddef"],"255":["Tanzania, United Republic of Tanzania","\ud83c\uddf9\ud83c\uddff"],"66":["Thailand","\ud83c\uddf9\ud83c\udded"],"670":["Timor-Leste","\ud83c\uddf9\ud83c\uddf1"],"228":["Togo","\ud83c\uddf9\ud83c\uddec"],"690":["Tokelau","\ud83c\uddf9\ud83c\uddf0"],"676":["Tonga","\ud83c\uddf9\ud83c\uddf4"],"1868":["Trinidad and Tobago","\ud83c\uddf9\ud83c\uddf9"],"216":["Tunisia","\ud83c\uddf9\ud83c\uddf3"],"90":["Turkey","\ud83c\uddf9\ud83c\uddf7"],"993":["Turkmenistan","\ud83c\uddf9\ud83c\uddf2"],"1649":["Turks and Caicos Islands","\ud83c\uddf9\ud83c\udde8"],"688":["Tuvalu","\ud83c\uddf9\ud83c\uddfb"],"256":["Uganda","\ud83c\uddfa\ud83c\uddec"],"380":["Ukraine","\ud83c\uddfa\ud83c\udde6"],"971":["United Arab Emirates","\ud83c\udde6\ud83c\uddea"],"598":["Uruguay","\ud83c\uddfa\ud83c\uddfe"],"998":["Uzbekistan","\ud83c\uddfa\ud83c\uddff"],"678":["Vanuatu","\ud83c\uddfb\ud83c\uddfa"],"58":["Venezuela, Bolivarian Republic of Venezuela","\ud83c\uddfb\ud83c\uddea"],"84":["Vietnam","\ud83c\uddfb\ud83c\uddf3"],"1284":["Virgin Islands, British","\ud83c\uddfb\ud83c\uddec"],"1340":["Virgin Islands, U.S.","\ud83c\uddfb\ud83c\uddee"],"681":["Wallis and Futuna","\ud83c\uddfc\ud83c\uddeb"],"967":["Yemen","\ud83c\uddfe\ud83c\uddea"],"260":["Zambia","\ud83c\uddff\ud83c\uddf2"],"263":["Zimbabwe","\ud83c\uddff\ud83c\uddfc"]}';
        $countryMapping = json_decode($country_data, true);

        $score = 0;
        $arr = [];
        foreach(array_keys($countryMapping) as $ccode){
            $countryMapping[$ccode][] = $ccode;
            $score = $key = 0;
            foreach(str_split($countryCode) as $code1){
                $key++;
                if($key==1 && $code1 == substr($ccode,0,1)) $score += 6; 
                if($key==2 && (substr($ccode,1,1)!="") && $code1 == substr($ccode,1,1)) $score += (strlen($ccode) == 2?5:2); 
                if($key==3 && (substr($ccode,2,1)!="") && $code1 == substr($ccode,2,1)) $score += 1; 
                if($key==4 && (substr($ccode,3,1)!="") && $code1 == substr($ccode,3,1)) $score += 1; 
            }

            $score += ($ccode == substr( $phoneNumber, 1, strlen($ccode)))?5:-5;
            if($score >= 3)
                $arr[$ccode] = $score;
        }

        $countryCode = array_search(max($arr), $arr);

        // Return country name or '' if not found
        return isset($countryMapping[$countryCode][0]) ? $countryMapping[$countryCode] : '';
    }
    return "";
}

// PHP 7.2 - ISO 3166-1 alpha-2 (officially assigned) -> English short name
function ocd_country_from_code($code) {
    static $countries = [ 'AD' => 'Andorra', 'AE' => 'United Arab Emirates', 'AF' => 'Afghanistan', 'AG' => 'Antigua and Barbuda', 'AI' => 'Anguilla', 'AL' => 'Albania', 'AM' => 'Armenia', 'AO' => 'Angola', 'AQ' => 'Antarctica', 'AR' => 'Argentina', 'AS' => 'American Samoa', 'AT' => 'Austria', 'AU' => 'Australia', 'AW' => 'Aruba', 'AX' => 'Åland Islands', 'AZ' => 'Azerbaijan', 'BA' => 'Bosnia and Herzegovina', 'BB' => 'Barbados', 'BD' => 'Bangladesh', 'BE' => 'Belgium', 'BF' => 'Burkina Faso', 'BG' => 'Bulgaria', 'BH' => 'Bahrain', 'BI' => 'Burundi', 'BJ' => 'Benin', 'BL' => 'Saint Barthélemy', 'BM' => 'Bermuda', 'BN' => 'Brunei Darussalam', 'BO' => 'Bolivia, Plurinational State of', 'BQ' => 'Bonaire, Sint Eustatius and Saba', 'BR' => 'Brazil', 'BS' => 'Bahamas', 'BT' => 'Bhutan', 'BV' => 'Bouvet Island', 'BW' => 'Botswana', 'BY' => 'Belarus', 'BZ' => 'Belize', 'CA' => 'Canada', 'CC' => 'Cocos (Keeling) Islands', 'CD' => 'Congo, Democratic Republic of the', 'CF' => 'Central African Republic', 'CG' => 'Congo', 'CH' => 'Switzerland', 'CI' => "Côte d'Ivoire", 'CK' => 'Cook Islands', 'CL' => 'Chile', 'CM' => 'Cameroon', 'CN' => 'China', 'CO' => 'Colombia', 'CR' => 'Costa Rica', 'CU' => 'Cuba', 'CV' => 'Cabo Verde', 'CW' => 'Curaçao', 'CX' => 'Christmas Island', 'CY' => 'Cyprus', 'CZ' => 'Czechia', 'DE' => 'Germany', 'DJ' => 'Djibouti', 'DK' => 'Denmark', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'DZ' => 'Algeria', 'EC' => 'Ecuador', 'EE' => 'Estonia', 'EG' => 'Egypt', 'EH' => 'Western Sahara', 'ER' => 'Eritrea', 'ES' => 'Spain', 'ET' => 'Ethiopia', 'FI' => 'Finland', 'FJ' => 'Fiji', 'FK' => 'Falkland Islands (Malvinas)', 'FM' => 'Micronesia, Federated States of', 'FO' => 'Faroe Islands', 'FR' => 'France', 'GA' => 'Gabon', 'GB' => 'United Kingdom', 'GD' => 'Grenada', 'GE' => 'Georgia', 'GF' => 'French Guiana', 'GG' => 'Guernsey', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GL' => 'Greenland', 'GM' => 'Gambia', 'GN' => 'Guinea', 'GP' => 'Guadeloupe', 'GQ' => 'Equatorial Guinea', 'GR' => 'Greece', 'GS' => 'South Georgia and the South Sandwich Islands', 'GT' => 'Guatemala', 'GU' => 'Guam', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HK' => 'Hong Kong', 'HM' => 'Heard Island and McDonald Islands', 'HN' => 'Honduras', 'HR' => 'Croatia', 'HT' => 'Haiti', 'HU' => 'Hungary', 'ID' => 'Indonesia', 'IE' => 'Ireland', 'IL' => 'Israel', 'IM' => 'Isle of Man', 'IN' => 'India', 'IO' => 'British Indian Ocean Territory', 'IQ' => 'Iraq', 'IR' => 'Iran, Islamic Republic of', 'IS' => 'Iceland', 'IT' => 'Italy', 'JE' => 'Jersey', 'JM' => 'Jamaica', 'JO' => 'Jordan', 'JP' => 'Japan', 'KE' => 'Kenya', 'KG' => 'Kyrgyzstan', 'KH' => 'Cambodia', 'KI' => 'Kiribati', 'KM' => 'Comoros', 'KN' => 'Saint Kitts and Nevis', 'KP' => "Korea, Democratic People's Republic of", 'KR' => 'Korea, Republic of', 'KW' => 'Kuwait', 'KY' => 'Cayman Islands', 'KZ' => 'Kazakhstan', 'LA' => "Lao People's Democratic Republic", 'LB' => 'Lebanon', 'LC' => 'Saint Lucia', 'LI' => 'Liechtenstein', 'LK' => 'Sri Lanka', 'LR' => 'Liberia', 'LS' => 'Lesotho', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'LV' => 'Latvia', 'LY' => 'Libya', 'MA' => 'Morocco', 'MC' => 'Monaco', 'MD' => 'Moldova, Republic of', 'ME' => 'Montenegro', 'MF' => 'Saint Martin (French part)', 'MG' => 'Madagascar', 'MH' => 'Marshall Islands', 'MK' => 'North Macedonia', 'ML' => 'Mali', 'MM' => 'Myanmar', 'MN' => 'Mongolia', 'MO' => 'Macao', 'MP' => 'Northern Mariana Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MS' => 'Montserrat', 'MT' => 'Malta', 'MU' => 'Mauritius', 'MV' => 'Maldives', 'MW' => 'Malawi', 'MX' => 'Mexico', 'MY' => 'Malaysia', 'MZ' => 'Mozambique', 'NA' => 'Namibia', 'NC' => 'New Caledonia', 'NE' => 'Niger', 'NF' => 'Norfolk Island', 'NG' => 'Nigeria', 'NI' => 'Nicaragua', 'NL' => 'Netherlands', 'NO' => 'Norway', 'NP' => 'Nepal', 'NR' => 'Nauru', 'NU' => 'Niue', 'NZ' => 'New Zealand', 'OM' => 'Oman', 'PA' => 'Panama', 'PE' => 'Peru', 'PF' => 'French Polynesia', 'PG' => 'Papua New Guinea', 'PH' => 'Philippines', 'PK' => 'Pakistan', 'PL' => 'Poland', 'PM' => 'Saint Pierre and Miquelon', 'PN' => 'Pitcairn', 'PR' => 'Puerto Rico', 'PS' => 'Palestine, State of', 'PT' => 'Portugal', 'PW' => 'Palau', 'PY' => 'Paraguay', 'QA' => 'Qatar', 'RE' => 'Réunion', 'RO' => 'Romania', 'RS' => 'Serbia', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'SA' => 'Saudi Arabia', 'SB' => 'Solomon Islands', 'SC' => 'Seychelles', 'SD' => 'Sudan', 'SE' => 'Sweden', 'SG' => 'Singapore', 'SH' => 'Saint Helena, Ascension and Tristan da Cunha', 'SI' => 'Slovenia', 'SJ' => 'Svalbard and Jan Mayen', 'SK' => 'Slovakia', 'SL' => 'Sierra Leone', 'SM' => 'San Marino', 'SN' => 'Senegal', 'SO' => 'Somalia', 'SR' => 'Suriname', 'SS' => 'South Sudan', 'ST' => 'Sao Tome and Principe', 'SV' => 'El Salvador', 'SX' => 'Sint Maarten (Dutch part)', 'SY' => 'Syrian Arab Republic', 'SZ' => 'Eswatini', 'TC' => 'Turks and Caicos Islands', 'TD' => 'Chad', 'TF' => 'French Southern Territories', 'TG' => 'Togo', 'TH' => 'Thailand', 'TJ' => 'Tajikistan', 'TK' => 'Tokelau', 'TL' => 'Timor-Leste', 'TM' => 'Turkmenistan', 'TN' => 'Tunisia', 'TO' => 'Tonga', 'TR' => 'Türkiye', 'TT' => 'Trinidad and Tobago', 'TV' => 'Tuvalu', 'TW' => 'Taiwan, Province of China', 'TZ' => 'Tanzania', 'UA' => 'Ukraine', 'UG' => 'Uganda', 'UM' => 'United States Minor Outlying Islands', 'US' => 'United States of America', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VA' => 'Holy See', 'VC' => 'Saint Vincent and the Grenadines', 'VE' => 'Venezuela, Bolivarian Republic of', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'VN' => 'Viet Nam', 'VU' => 'Vanuatu', 'WF' => 'Wallis and Futuna', 'WS' => 'Samoa', 'YE' => 'Yemen', 'YT' => 'Mayotte', 'ZA' => 'South Africa', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe', ];

    $code = strtoupper(trim((string)$code));
    return $countries[$code] ?? null; // null if unknown
}


// get browser data
function ocd_get_browser_client_info(){
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // --- IP (handles common proxy/CDN headers) ---
    $ip = null;
    $keys = [
        'HTTP_CF_CONNECTING_IP',   // Cloudflare
        'HTTP_X_FORWARDED_FOR',    // proxies (may be "client, proxy1, proxy2")
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR'
    ];

    foreach ($keys as $k) {
        if (!empty($_SERVER[$k])) {
            $candidate = $_SERVER[$k];
            if ($k === 'HTTP_X_FORWARDED_FOR') {
                $parts = array_map('trim', explode(',', $candidate));
                $candidate = $parts[0] ?? $candidate;
            }
            if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                $ip = $candidate;
                break;
            }
        }
    }

    // --- Platform (OS) best-effort ---
    $platform = 'Unknown';
    $uaLower = strtolower($ua);
    if (strpos($uaLower, 'windows') !== false) $platform = 'Windows';
    elseif (strpos($uaLower, 'mac os x') !== false || strpos($uaLower, 'macintosh') !== false) $platform = 'Mac';
    elseif (strpos($uaLower, 'android') !== false) $platform = 'Android';
    elseif (strpos($uaLower, 'iphone') !== false || strpos($uaLower, 'ipad') !== false || strpos($uaLower, 'ios') !== false) $platform = 'iOS';
    elseif (strpos($uaLower, 'linux') !== false) $platform = 'Linux';

    // --- Browser + version best-effort ---
    $browser = 'Unknown';
    $version = '';

    // Note: Order matters (Edge/Opera include "Chrome" in UA)
    $map = [
        'Edg/'    => 'Edge',
        'OPR/'    => 'Opera',
        'Chrome/' => 'Chrome',
        'Firefox/' => 'Firefox',
        'Safari/' => 'Safari',
        'MSIE '   => 'Internet Explorer',
        'Trident/' => 'Internet Explorer',
    ];

    foreach ($map as $token => $name) {
        if (strpos($ua, $token) !== false) {
            $browser = $name;

            // Special cases
            if ($name === 'Internet Explorer') {
                if (preg_match('/MSIE\s([0-9\.]+)/', $ua, $m)) $version = $m[1];
                elseif (preg_match('/rv:([0-9\.]+)/', $ua, $m)) $version = $m[1];
            } elseif ($name === 'Safari') {
                // Safari version is usually in "Version/x.y"
                if (preg_match('/Version\/([0-9\.]+)/', $ua, $m)) $version = $m[1];
            } else {
                // Standard token/x.y.z
                $pattern = '/' . preg_quote($token, '/') . '([0-9\.]+)/';
                if (preg_match($pattern, $ua, $m)) $version = $m[1];
            }

            break;
        }
    }

    return [
        'browser'         => $browser,
        'browser_version' => $version,
        'user_agent'      => $ua,
        'platform'        => $platform,
        'ip_address'      => $ip,
    ];
}

function ocd_encrypt($data, $key=''){

    $CI =& get_instance();

    if($key == '')
        $key = $CI->config->item('encryption_key');
    
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

    $result = base64_encode($iv . $encrypted);

    // Make URL safe (remove + / =)
    return rtrim(strtr($result, '+/', '-_'), '=');
}

function ocd_decrypt($data, $key=''){

    $CI =& get_instance();
    
    if($key == '')
        $key = $CI->config->item('encryption_key');

    // Restore base64
    $data = strtr($data, '-_', '+/');
    $data = base64_decode($data);

    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);

    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}
