<?php

namespace xjryanse\wechat\service\wePub;

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

    public function fLogo() {
        return $this->getFFieldValue(__FUNCTION__);
    }
}
