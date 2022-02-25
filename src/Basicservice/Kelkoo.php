<?php
namespace geekshare\shopapi\Basicservice;
class Kelkoo{
    private $country,$partner,$key;

    public function __construct($country,$partner,$key){
        $this->country = $country;
        $this->partner = $partner;
        $this->key = $key;
    }

    public function get_data_kelkoo($keywords,$pagenumber=20,$page=1){
        //basic params
        $data["logicalType"]="or";
        $data["sort"]="default_ranking";
        $data["show_products"]=1;
        $data["show_subcategories"]=0;
        $data["show_refinements"]=0;
        $data["query"] = $keywords;
    
        $start=$page*$pagenumber;
        $results=$pagenumber;
    
        $line="?start=".$start."&results=".$results;
    
        foreach($data as $k=>$val){
            $line.="&".$k."=".$val;
        }
        $url = $this->UrlSigner('http://'.$this->country.'.shoppingapis.kelkoo.com', '/V3/productSearch'.$line, $this->partner, $this->key);

        $xml = $this->get_url($url);
        
        $list = simplexml_load_string($xml);

        $data=[];
        
        if(!empty($list)){
            $data["status"] = "1";
            $data["product"] = $this->kelkoo_prodect($list->Products->Product);
            $data["count"] = $list->Products->attributes()->totalResultsAvailable;
        }else{
            $data["status"] = "0";
            $data["product"]=[];
            $data["count"]=0;
        }

        return $data;
    }

    private function kelkoo_prodect($data){
        $return_result=[];
        foreach ($data as $k=>$val){
            $result['id'] = (string)$val->Offer->attributes()->id;
            $result['titles'] = (string)$val->Offer->Title;
            $result['brand'] = (string)$val->Offer->Brand;
            $result['url'] = (string)$val->Offer->Url;
            $result['img'] = (string)$val->Offer->Images->ZoomImage->Url;
            $result['des'] = (string)$val->Offer->Description;
            $result['price'] = (string)$val->Offer->Price->Price;
            $result['old_price'] = (string)$val->Offer->Price->TotalPrice;
            $result['s_type'] = 'shipping';
            array_push($return_result,$result);
        }
        return $return_result;
    }

    private function get_url($url)
    {
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-Encoding: gzip,deflate"
            )
        );
        $context = stream_context_create($opts);
        $content = file_get_contents($url, false, $context);
        $content = gzinflate(substr($content, 10, -8));
        return $content;
    }

    private function UrlSigner($urlDomain, $urlPath, $partner, $key){
        settype($urlDomain, 'String');
        settype($urlPath, 'String');
        settype($partner, 'String');
        settype($key, 'String');

        $URL_sig = "hash";
        $URL_ts = "timestamp";
        $URL_partner = "aid";
        $URLreturn = "";
        $URLtmp = "";
        $s = "";
        // get the timestamp
        $time = time();

        // replace " " by "+"
        $urlPath = str_replace(" ", "+", $urlPath);
        // format URL
        $URLtmp = $urlPath . "&" . $URL_partner . "=" . $partner . "&" . $URL_ts . "=" . $time;

        // URL needed to create the tokken
        $s = $urlPath . "&" . $URL_partner . "=" . $partner . "&" . $URL_ts . "=" . $time . $key;
        $tokken = "";
        $tokken = base64_encode(pack('H*', md5($s)));
        $tokken = str_replace(array("+", "/", "="), array(".", "_", "-"), $tokken);
        $URLreturn = $urlDomain . $URLtmp . "&" . $URL_sig . "=" . $tokken;
        return $URLreturn;
    }
}
?>