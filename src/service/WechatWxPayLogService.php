<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信支付记录
 */
class WechatWxPayLogService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWxPayLog';

    /**
     * 拆解val数据保存
     */
    public function tearValData() {
        $info = $this->get();
        //val转数组
        $data = json_decode($info['val'], true);
        return $this->update($data);
    }

    /**
     * 根据商户单号获取支付信息
     */
    public static function getByOutTradeNo($paySn, $con = []) {
        $con[] = ['out_trade_no', '=', $paySn];
        $con[] = ['result_code', '=', 'SUCCESS'];
        $con[] = ['return_code', '=', 'SUCCESS'];

        return self::find($con);
    }

    /**
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 返回信息
     */
    public function fVal() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fAttach() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fBankType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCashFee() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fFeeType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fIsSubscribe() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fMchId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fNonceStr() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fOpenid() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fOutTradeNo() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fResultCode() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fReturnCode() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fSign() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fTimeEnd() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fTotalFee() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fTradeType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fTransactionId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 状态(0禁用,1启用)
     */
    public function fStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 有使用(0否,1是)
     */
    public function fHasUsed() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未锁，1：已锁）
     */
    public function fIsLock() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未删，1：已删）
     */
    public function fIsDelete() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者，user表
     */
    public function fUpdater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新时间
     */
    public function fUpdateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
