<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-cms
// | github：https://github.com/sveil/zimeiti-cms
// +----------------------------------------------------------------------

namespace sveil\rep\wechat;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * Coupon management
 *
 * Class Card
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Card extends WeChat
{

    /**
     * Create Coupon
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create(array $data)
    {

        $url = "https://api.weixin.qq.com/card/create?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Set up pay interface
     *
     * @param string $card_id
     * @param bool $is_open
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setPaycell($card_id, $is_open = true)
    {

        $url = "https://api.weixin.qq.com/card/paycell/set?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id, 'is_open' => $is_open]);
    }

    /**
     * Set up self-checkout interface
     *
     * @param string $card_id
     * @param bool $is_open
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setConsumeCell($card_id, $is_open = true)
    {

        $url = "https://api.weixin.qq.com/card/selfconsumecell/set?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id, 'is_open' => $is_open]);
    }

    /**
     * Create QR code interface
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function createQrc(array $data)
    {

        $url = "https://api.weixin.qq.com/card/qrcode/create?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Create shelf interface
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function createLandingPage(array $data)
    {

        $url = "https://api.weixin.qq.com/card/landingpage/create?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Import custom code
     *
     * @param string $card_id
     * @param array $code
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deposit($card_id, array $code)
    {

        $url = "https://api.weixin.qq.com/card/code/deposit?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id, 'code' => $code]);
    }

    /**
     * Query the number of imported codes
     *
     * @param string $card_id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getDepositCount($card_id)
    {

        $url = "https://api.weixin.qq.com/card/code/getdepositcount?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id]);
    }

    /**
     * Check code interface
     *
     * @param string $card_id Card ID for importing code
     * @param array $code The custom code of the WeChat card coupon background，The limit is 100
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function checkCode($card_id, array $code)
    {

        $url = "https://api.weixin.qq.com/card/code/checkcode?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id, 'code' => $code]);
    }

    /**
     * Graphic message group card coupon
     *
     * @param string $card_id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getNewsHtml($card_id)
    {

        $url = "https://api.weixin.qq.com/card/mpnews/gethtml?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id]);
    }

    /**
     * Set up a test whitelist
     *
     * @param array $openids
     * @param array $usernames
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setTestWhiteList($openids = [], $usernames = [])
    {

        $url = "https://api.weixin.qq.com/card/testwhitelist/set?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['openid' => $openids, 'username' => $usernames]);
    }

    /**
     * Offline verification query Code
     *
     * @param string $code The only standard for a single card
     * @param string $card_id Card ID represents a type of card coupon, custom code card coupon is required
     * @param bool $check_consume Whether to verify code verification status,
     * the data returned when the code abnormal state is filled when true and false are filled
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCode($code, $card_id = null, $check_consume = null)
    {

        $data                                             = ['code' => $code];
        is_null($card_id) || $data['card_id']             = $card_id;
        is_null($check_consume) || $data['check_consume'] = $check_consume;
        $url                                              = "https://api.weixin.qq.com/card/code/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Offline consume code
     *
     * @param string $code Code to be consumed
     * @param null $card_id Coupon ID. Use_custom_code is required to fill in true when creating a coupon.
     * Non custom code does not need to be filled.
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function consume($code, $card_id = null)
    {

        $data                                 = ['code' => $code];
        is_null($card_id) || $data['card_id'] = $card_id;
        $url                                  = "https://api.weixin.qq.com/card/code/consume?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Code decoding interface
     *
     * @param string $encrypt_code
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function decrypt($encrypt_code)
    {

        $url = "https://api.weixin.qq.com/card/code/decrypt?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['encrypt_code' => $encrypt_code]);
    }

    /**
     * Get user's card coupon interface
     *
     * @param string $openid
     * @param null|string $card_id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCardList($openid, $card_id = null)
    {

        $data                                 = ['openid' => $openid];
        is_null($card_id) || $data['card_id'] = $card_id;
        $url                                  = "https://api.weixin.qq.com/card/user/getcardlist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * View card coupon details
     *
     * @param string $card_id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCard($card_id)
    {

        $url = "https://api.weixin.qq.com/card/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id]);
    }

    /**
     * Batch query card coupon list
     *
     * @param int $offset Query the starting offset of the card list，Start from 0，That is, offset: 5 means to read from the sixth in the list
     * @param int $count Number of cards to be queried（Maximum 50）
     * @param array $status_list Support developers to pull out a list of designated coupons
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function batchGet($offset, $count = 50, array $status_list = [])
    {

        $data                                       = ['offset' => $offset, 'count' => $count];
        empty($status_list) || $data['status_list'] = $status_list;
        $url                                        = "https://api.weixin.qq.com/card/batchget?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Change card coupon information interface
     *
     * @param string $card_id
     * @param array $member_card
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateCard($card_id, array $member_card)
    {

        $url = "https://api.weixin.qq.com/card/update?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id, 'member_card' => $member_card]);
    }

    /**
     * Modify inventory interface
     *
     * @param string $card_id Coupon id
     * @param null|integer $increase_stock_value How much inventory to increase，Support not fill or fill 0
     * @param null|integer $reduce_stock_value How much inventory is reduced，Can not fill in or fill in 0
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function modifyStock($card_id, $increase_stock_value = null, $reduce_stock_value = null)
    {

        $data                                                           = ['card_id' => $card_id];
        is_null($increase_stock_value) || $data['increase_stock_value'] = $increase_stock_value;
        is_null($reduce_stock_value) || $data['reduce_stock_value']     = $reduce_stock_value;
        $url                                                            = "https://api.weixin.qq.com/card/modifystock?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Change code coupon interface
     *
     * @param string $code Code to be changed
     * @param string $new_code Effective code after change
     * @param null|string $card_id Coupon ID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateCode($code, $new_code, $card_id = null)
    {

        $data                                 = ['code' => $code, 'new_code' => $new_code];
        is_null($card_id) || $data['card_id'] = $card_id;
        $url                                  = "https://api.weixin.qq.com/card/code/update?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Delete card coupon interface
     *
     * @param string $card_id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deleteCard($card_id)
    {

        $url = "https://api.weixin.qq.com/card/delete?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id]);
    }

    /**
     * Set card coupon invalid interface
     *
     * @param string $code
     * @param string $card_id
     * @param null|string $reason
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function unAvailable($code, $card_id, $reason = null)
    {

        $data                               = ['code' => $code, 'card_id' => $card_id];
        is_null($reason) || $data['reason'] = $reason;
        $url                                = "https://api.weixin.qq.com/card/code/unavailable?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Pull card coupon profile data interface
     *
     * @param string $begin_date Start time of query data
     * @param string $end_date Deadline for querying data
     * @param string $cond_source Coupon source(0 Card and coupon data created for the public platform, 1 is the coupon data created by API)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCardBizuininfo($begin_date, $end_date, $cond_source)
    {

        $data = ['begin_date' => $begin_date, 'end_date' => $end_date, 'cond_source' => $cond_source];
        $url  = "https://api.weixin.qq.com/datacube/getcardbizuininfo?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Get free coupon data interface
     *
     * @param string $begin_date Start time of query data
     * @param string $end_date Deadline for querying data
     * @param integer $cond_source Coupon source，0 Card and coupon data created for the public platform, 1 is the coupon data created by API
     * @param null $card_id Coupon id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCardCardinfo($begin_date, $end_date, $cond_source, $card_id = null)
    {

        $data                                 = ['begin_date' => $begin_date, 'end_date' => $end_date, 'cond_source' => $cond_source];
        is_null($card_id) || $data['card_id'] = $card_id;
        $url                                  = "https://api.weixin.qq.com/datacube/getcardcardinfo?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Activate membership card
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function activateMemberCard(array $data)
    {

        $url = 'https://api.weixin.qq.com/card/membercard/activate?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Set the card opening field interface, Options that need to be filled in when the user activates
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setActivateMemberCardUser(array $data)
    {

        $url = 'https://api.weixin.qq.com/card/membercard/activateuserform/set?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Get user submission, Obtain the information filled in by the user according to activate_ticket
     *
     * @param string $activate_ticket
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getActivateMemberCardTempinfo($activate_ticket)
    {

        $url = 'https://api.weixin.qq.com/card/membercard/activatetempinfo/get?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['activate_ticket' => $activate_ticket]);
    }

    /**
     * Update member information
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateMemberCardUser(array $data)
    {

        $url = 'https://api.weixin.qq.com/card/membercard/updateuser?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Membership card profile data interface
     *
     * @param string $begin_date Start time of query data
     * @param string $end_date Deadline for querying data
     * @param string $cond_source Coupon source，0 Card and coupon data created for the public platform, 1 is the coupon data created by API
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCardMemberCardinfo($begin_date, $end_date, $cond_source)
    {

        $data = ['begin_date' => $begin_date, 'end_date' => $end_date, 'cond_source' => $cond_source];
        $url  = "https://api.weixin.qq.com/datacube/getcardmembercardinfo?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Data interface for pulling a single membership card
     *
     * @param string $begin_date Start time of query data
     * @param string $end_date Deadline for querying data
     * @param string $card_id Coupon id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCardMemberCardDetail($begin_date, $end_date, $card_id)
    {

        $data = ['begin_date' => $begin_date, 'end_date' => $end_date, 'card_id' => $card_id];
        $url  = "https://api.weixin.qq.com/datacube/getcardmembercarddetail?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Pull Member Information (Points Query) Interface
     *
     * @param string $card_id Check the cardid of the membership card
     * @param string $code The code value received by the queried user
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCardMemberCard($card_id, $code)
    {

        $data = ['card_id' => $card_id, 'code' => $code];
        $url  = "https://api.weixin.qq.com/card/membercard/userinfo/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Set up an interface to post cards and coupons after payment
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function payGiftCard(array $data)
    {

        $url = "https://api.weixin.qq.com/card/paygiftcard/add?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Delete card and coupon rules after payment
     *
     * @param integer $rule_id Payment is the member's rule name
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delPayGiftCard($rule_id)
    {

        $url = "https://api.weixin.qq.com/card/paygiftcard/add?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['rule_id' => $rule_id]);
    }

    /**
     * Check the details of the rules for issuing cards and coupons after payment
     *
     * @param integer $rule_id To query the rule id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getPayGiftCard($rule_id)
    {

        $url = "https://api.weixin.qq.com/card/paygiftcard/getbyid?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['rule_id' => $rule_id]);
    }

    /**
     * Batch query card and coupon rules after payment
     *
     * @param integer $offset Starting offset
     * @param integer $count Number of inquiries
     * @param bool $effective Whether to query only the rules in effect
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function batchGetPayGiftCard($offset = 0, $count = 10, $effective = true)
    {

        $data = ['type' => 'RULE_TYPE_PAY_MEMBER_CARD', 'offset' => $offset, 'count' => $count, 'effective' => $effective];
        $url  = "https://api.weixin.qq.com/card/paygiftcard/batchget?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * After receiving the payment, receive a deduction
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addActivity(array $data)
    {

        $url = "https://api.weixin.qq.com/card/mkt/activity/create?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Open a coupon account account interface
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function payActivate()
    {

        $url = "https://api.weixin.qq.com/card/pay/activate?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Rate the coupon
     *
     * @param string $card_id Need to configure the card_id of the inventory
     * @param integer $quantity Number of stocks to be exchanged this time
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getPayprice($card_id, $quantity)
    {

        $url = "POST https://api.weixin.qq.com/card/pay/getpayprice?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['card_id' => $card_id, 'quantity' => $quantity]);
    }

    /**
     * Query coupon balance interface
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCoinsInfo()
    {

        $url = "https://api.weixin.qq.com/card/pay/getcoinsinfo?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Confirm exchange inventory interface
     *
     * @param string $card_id Card_id needed to redeem inventory
     * @param integer $quantity Number of stocks to be exchanged this time
     * @param string $order_id Only the order number obtained above can be used to guarantee the validity of the batch price
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function payConfirm($card_id, $quantity, $order_id)
    {

        $data = ['card_id' => $card_id, 'quantity' => $quantity, 'order_id' => $order_id];
        $url  = "https://api.weixin.qq.com/card/pay/confirm?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Coupon interface
     *
     * @param integer $coin_count
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function payRecharge($coin_count)
    {

        $url = "https://api.weixin.qq.com/card/pay/recharge?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['coin_count' => $coin_count]);
    }

    /**
     * Interface for querying order details
     *
     * @param string $order_id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function payGetOrder($order_id)
    {

        $url = "https://api.weixin.qq.com/card/pay/getorder?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['order_id' => $order_id]);
    }

    /**
     * Interface for querying coupon details
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function payGetList(array $data)
    {

        $url = "https://api.weixin.qq.com/card/pay/getorderlist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

}
