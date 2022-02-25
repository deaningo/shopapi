<?php 
class PhpCurl{
    protected $_url;
    protected $_params;
    protected $_post;
    protected $_https;
    protected $_header;
    protected $_ua;
    protected $_reffer;
    protected $_timeout;

    public function _construct($url,$headers = array(),$params,$https,$timeOut = 30){
        $this->_url = $url;
        $this->_headers = $headers;
        $this->_params = $params;
        $this->_https = $https;
    }

    public function setPost(){
        if($this->_post){
            $this->_post = true;
        }
    }

    public function setHttps(){
        if($this->_https){
            $this->_https = true;
        }
    }

    public function setUserAgent($useragent){
        $this->_ua  = $useragent;
    }

    public function setReffer($reffer){
        $this->_reffer = $reffer;
    }

    public function Curl($url='null'){
        if($url != null){
            $this->_url = $url;
        }

        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_ua);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($this->_https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }

        if ($this->_post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($this->_params) {
                if (is_array($this->_params)) {
                    $this->_params = http_build_query($this->_params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $this->_params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }        
}
?>