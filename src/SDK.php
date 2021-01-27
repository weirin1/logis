<?php

namespace Weirin\Logis;

/**
 *  快递鸟接口：http://www.kdniao.com/api-track
 *  @author  Lcn <378107001@qq.com>
 *  @version 1.0
 * Class SDK
 * @package Logis
 */
class SDK
{
    const API_URL = "http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx";

    private $uid;
    private $appkey;

    /**
     * @param $options
     */
    public function __construct($options)
    {
        $this->uid = isset($options['kdniao_uid']) ? $options['kdniao_uid'] : '';
        $this->appkey = isset($options['kdniao_appkey']) ? $options['kdniao_appkey'] : '';
    }

    /**
     * Json方式 查询订单物流轨迹
     * @param $shipperCode
     * @param $logisticCode
     */
    public function getOrderTracesByJson($shipperCode, $logisticCode)
    {
        $requestData = [
            'ShipperCode' => $shipperCode,
            'LogisticCode' => trim($logisticCode)
        ];
        $requestData = json_encode($requestData);

        $datas = array(
            'EBusinessID' => $this->uid,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData);
        $response = $this->sendPost(self::API_URL, $datas);

        $result = [];
        if (is_array($response)) {
            foreach ($response as $key => $value){
                $result[strtolower($key)] = $value;
            }
        }

        return $result;
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return array
     */
    private function sendPost($url, $datas)
    {
        $result = Http::post($url, $datas);
        return json_decode($result, true);
    }

    /**
     * 电商Sign签名生成
     * @param $data
     * @param $appkey
     * @return string
     */
    private function encrypt($data)
    {
        return urlencode(base64_encode(md5($data . $this->appkey)));
    }
}
