<?php
namespace Funmula\BiggoApiPhpPmsClient;

class BiggoPMSAPI
{
  private string $clientID      = "";
  private string $clientSecret  = "";
  private string $accessToken   = "";
  private string $token_type    = "";
  private float  $expiresAt     = 0;
  private string $baseURL       = "";


  public function __construct($clientID, $clientSecret)
  {
    $this->clientID = $clientID;
    $this->clientSecret = $clientSecret;
    $this->baseURL = 'https://api.biggo.com/api/v1/pms';
    $this->renewToken();
  }

  /**
   * set value
   * @param   string      $name   val name
   * @param   string      $val    val
   */
  public function setValue($name, $val)
  {
    if (isset($this->{$name})) {
      $this->{$name} = $val;
    }
  }

  /**
   * get Token
   * @var     string      $clientID         clientID
   * @var     string      $clientSecret     clientSecret
   */
  private function renewToken()
  {
    $basicAuthHeader = 'Basic ' . base64_encode($this->clientID . ":" . $this->clientSecret);
    //get Token
    $url = "https://api.biggo.com/auth/v1/token";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('grant_type' => "client_credentials")));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:$basicAuthHeader", "Content-Type:application/json"));
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true) ?? array();
    if (isset($response['result']) &&  $response['result'] == false) {
      throw new \RuntimeException("message:" . $response['error']['message']);
    }

    $this->accessToken = $response['access_token'];
    $this->token_type = $response['token_type'] === 'bearer' ? 'Bearer' : $response['token_type'];
    $this->expiresAt = strtotime('now') + $response['expires_in'];

  }

  /**
   * Check the token is expired.
   * @var     string    $expiresAt  expired time
   * @return  bool
   */
  public function isTokenExpired()
  {
    return (strtotime('now') > $this->expiresAt);
  }

  /**
   * get Token
   * @var     string    $accessToken    token
   * @return  string
   */
  private function getToken()
  {
    if(empty($this->accessToken) || $this->isTokenExpired()){
      $this->renewToken();
    }
    return $this->accessToken;
  }

  /**
   * Get this user's platform list.
   */
  public function getPlatformList()
  {
    $url = '/platform';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseURL . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:" . $this->token_type . " " . $this->getToken()));
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true) ?? array();
    if(empty($response) || $response['result'] == false){
      throw new \RuntimeException("error_code:" . $response['error_code'] . "\nmessage:" . $response['error']);
    }
    $response = $response['data'];
    $data = array();
    foreach($response as $r){
      $data[] = [
        'id' => $r['_id'],
        'name' => $r['platform_name'],
        'status' => $r['status'] ?? 'enable',
        'userList' => $r['userid_list'],
        'emailList' => $r['email_list']
      ];
    }
    return $data;
  }

  /**
   * Get group list of the platform.
   * @param   string    $platformID   pms platform id
   */
  public function getGroupList($platformID = "")
  {
    $url = '/group';
    $body = [
      'pms_platformid' => $platformID,
    ];
    $body_string = http_build_query($body);
    $body_string = urldecode($body_string);
    $url = $url . "?" . $body_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseURL . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:" . $this->token_type . " " . $this->getToken()));
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true) ?? array();
    if(empty($response) || $response['result'] == false){
      throw new \RuntimeException("error_code:" . $response['error_code'] . "\nmessage:" . $response['error']);
    }
    $response = $response['data'];
    $data = array();
    foreach($response as $r){
      $data[] = [
        'id' => $r['_id'],
        'schedule' => $r['crontab_setting'],
        'isScheduleOn' => $r['crontab'] === 'true' ? true : false,
        'name' => $r['group_name'],
        'district' => $r['district'],
        'status' => $r['status'],
        'exportCount' => $r['export_count'],
        'sampleCount' => $r['sample_count']
      ];
    }
    return $data;
  }

  /**
   * Get group list of the platform.
   * @param   string    $platformID   pms platform id
   */
  public function getReportList($platformID = "", $options = array())
  {
    $url = '/export';
    $body = [
      'pms_platformid' => $platformID,
      'in_sort' => $options['in_sort'] ?? 'desc',
      'size' => $options['size'] ?? 5000,
      'in_form' => $options['in_form'] ?? 0,
    ];

    if(isset($options['pms_groupid']) && !empty($options['pms_groupid'])){
      $body['in_opt']['pms_groupid'] = $options['pms_groupid'];
    }
    if(isset($options['start']) && !empty($options['start'])){
      $body['in_opt']['start'] = $options['start'];
    }
    if(isset($options['end']) && !empty($options['end'])){
      $body['in_opt']['end'] = $options['end'];
    }

    $body_string = http_build_query($body);
    $body_string = urldecode($body_string);
    $url = $url . "?" . $body_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseURL . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:" . $this->token_type . " " . $this->getToken()));
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true) ?? array();
    if(empty($response) || $response['result'] == false){
      throw new \RuntimeException("error_code:" . $response['error_code'] . "\nmessage:" . $response['error']);
    }
    $response = $response['data'];
    $data = array();
    foreach($response as $r){
      $data[] = [
        'id' => $r['_id'],
        'createTime' => $r['createtime'],
        'groupID' => $r['pms_groupid'],
        'groupName' => $r['group_name'],
        'district' => $r['district'],
        'sampleSize' => $r['sample_size']
      ];
    }
    return $data;
  }

  /**
   * Download a History Report.
   * @param   string    $platformID   pms platform id
   * @param   string    $reportID     report id
   * @param   string    $fileType     type(csv | excel | json)
   * @param   bool      $saveAsFile   save to file
   */
  public function getReport($platformID = "", $reportID = "", $fileType = "", $saveAsFile = false, $filePath = "", $fileName = "")
  {
    $url = '/export/' . $reportID;
    $body = [
      'pms_platformid' => $platformID,
      'file_type' => $fileType
    ];
    $body_string = http_build_query($body);
    $body_string = urldecode($body_string);
    $url = $url . "?" . $body_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseURL . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:" . $this->token_type . " " . $this->getToken()));
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$headers)
    {
      $len = strlen($header);
      $header = explode(':', $header, 2);
      if (count($header) < 2)
        return $len;

      $headers[strtolower(trim($header[0]))][] = trim($header[1]);

      return $len;
    });
    $response = curl_exec($ch);
    curl_close($ch);
    $re = json_decode($response, true) ?? array();
    if(isset($re['result']) && $re['result'] == false){
      throw new \RuntimeException("error_code:" . $re['error_code'] . "\nmessage:" . $re['error']);
    }
    switch($fileType)
    {
      case 'csv':
        $data = "\xEF\xBB\xBF" . $response;
        $type = 'csv';
        break;
      case 'excel':
        $data = $response;
        $type = 'xlsx';
        break;
      case 'json':
        $data = $response;
        $type = 'json';
        break;
    }
    if($saveAsFile){
      if(empty($fileName)){
        $fileName = $headers['content-disposition'][0] ?? '';
        $fileName = explode('filename=', $fileName)[1] ?? 'BiggoPMS.' . $type;
        $fileName = urldecode($fileName);
      }
      if(empty($filePath)){
        $filePath = '.' ;
      }
      $fp = fopen($filePath. "/" . $fileName, 'w');
      fwrite($fp, $data);
      fclose($fp);
      return $filePath;
    }
    return $data;
  }
}

?>
