<?php
namespace xjryanse\wechat\WePub\wxurl;

class Datacube extends Base
{
    /**
     * 空方法，偷懒
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public function __call($name,$arguments) { 
        $class = (new \ReflectionClass( __CLASS__ ))->getShortName();
        return $this->getMyUrl($class, $name );
    }

}
