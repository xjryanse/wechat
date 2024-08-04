<?php

namespace xjryanse\wechat\service\wePubQrScene;

/**
 * 分页复用列表
 */
trait FieldTraits{

    /**
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fWePubId() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fLabel() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
}
