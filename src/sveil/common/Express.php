<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-lib
// | github：https://github.com/sveil/zimeiti-lib
// +----------------------------------------------------------------------

namespace sveil\common;

/**
 * Class Express
 * Express 100 query interface
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class Express
{
    /**
     * Query logistics information
     * @param string $code Express company editor
     * @param string $number Express logistics code number
     * @return array
     */
    public static function query($code, $number)
    {
        list($list, $cache) = [[], app()->cache->get($ckey = md5($code . $number))];

        if (!empty($cache)) {
            return ['message' => 'ok', 'com' => $code, 'nu' => $number, 'data' => $cache];
        }

        for ($i = 0; $i < 6; $i++) {
            if (is_array($result = self::doExpress($code, $number))) {
                if (!empty($result['data']['info']['context'])) {
                    foreach ($result['data']['info']['context'] as $vo) {
                        $list[] = [
                            'time' => date('Y-m-d H:i:s', $vo['time']), 'context' => $vo['desc'],
                        ];
                    }

                    app()->cache->set($ckey, $list, 10);

                    return ['message' => 'ok', 'com' => $code, 'nu' => $number, 'data' => $list];
                }
            }
        }

        return ['message' => 'ok', 'com' => $code, 'nu' => $number, 'data' => $list];
    }

    /**
     * Get a list of courier companies
     * @return array
     */
    public static function getExpressList()
    {
        $data = [];

        if (preg_match('/"currentData":.*?\[(.*?)\],/', self::getWapBaiduHtml(), $matches)) {
            foreach (json_decode("[{$matches['1']}]") as $item) {
                $data[$item->value] = $item->text;
            }

            unset($data['_auto']);

            return $data;
        } else {
            app()->cache->delete('express_kuaidi_html');

            return self::getExpressList();
        }
    }

    /**
     * Perform Baidu Express 100 application query request
     * @param string $code Courier company code number
     * @param string $number Courier code number
     * @return mixed
     */
    private static function doExpress($code, $number)
    {
        list($uniqid, $token) = [strtr(uniqid(), '.', ''), self::getExpressToken()];
        $url                  = "https://express.baidu.com/express/api/express?tokenV2={$token}&appid=4001&nu={$number}&com={$code}&qid={$uniqid}&new_need_di=1&source_xcx=0&vcode=&token=&sourceId=4155&cb=callback";

        return json_decode(str_replace('/**/callback(', '', trim(Http::get($url, [], self::getOption()), ')')), true);
    }

    /**
     * Get interface request token
     * @return string
     */
    private static function getExpressToken()
    {
        if (preg_match('/express\?tokenV2=(.*?)",/', self::getWapBaiduHtml(), $matches)) {
            return $matches[1];
        } else {
            app()->cache->delete('express_kuaidi_html');
            return self::getExpressToken();
        }
    }

    /**
     * Get Baidu WAP Express HTML
     * @return string
     */
    private static function getWapBaiduHtml()
    {
        $content = app()->cache->get('express_kuaidi_html');

        while (empty($content) || stristr($content, '百度安全验证') > -1 || stripos($content, 'tokenV2') === -1) {
            $content = Http::get('https://m.baidu.com/s?word=快递查询&rnd=' . uniqid(), [], self::getOption());
        }

        app()->cache->set('express_kuaidi_html', $content, 30);

        return $content;
    }

    /**
     * Get HTTP request configuration
     * @return array
     */
    private static function getOption()
    {
        return [
            'cookie_file' => app()->getRuntimePath() . '_express_cookie.txt',
            'headers'     => ['Host' => 'express.baidu.com', 'X-FORWARDED-FOR' => request()->ip()],
        ];
    }
}
