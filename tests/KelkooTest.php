<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use geekshare\shopapi\Basicservice\Kelkoo;

class KelkooTest extends TestCase{
    public function testGet(){
        $_this = $this;
        $country = 'xxxxxx';//uk,us
        $partner = "xxxxxx";
        $key = "xxxxxxx";

        $kelkoo = new Kelkoo($country,$partner,$key);

        $keyword = !empty($_GET['key'])?$_GET['key']:'dress';
        $page = !empty($_GET['page'])?$_GET['page']:1;
        $pagenum = 8;

        $result = $kelkoo->get_data_kelkoo($keyword,$pagenum,$page);
        $_this->assertEquals('1',$result['status'],'success');

    }
}