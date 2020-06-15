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

namespace sveil\lib\command;

use sveil\console\Command;
use sveil\console\Input;
use sveil\console\Output;
use sveil\Db;
use sveil\db\exception\DataNotFoundException;
use sveil\db\exception\ModelNotFoundException;
use sveil\Exception;
use sveil\exception\DbException;
use sveil\exception\PDOException;
use sveil\lib\common\We;

/**
 * Class AutoRun
 * Shopping mall data processing instructions
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command
 */
class AutoRun extends Command
{
    /**
     * 配置指令信息
     */
    protected function configure()
    {
        $this->setName('xclean:store')->setDescription('[清理]检查并处理商城任务');
    }

    /**
     * Business instruction execution
     * @param Input $input
     * @param Output $output
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    protected function execute(Input $input, Output $output)
    {
        // Automatically cancel 30 minute unpaid orders
        $this->autoCancelOrder();
        // Clean up orders that were not paid a day ago
        $this->autoRemoveOrder();
        // Order automatic refund processing
        // $this->autoRefundOrder();
        // Automatic cash withdrawal processing
        // $this->autoTransfer();
    }

    /**
     * Automatically cancel 30 minute unpaid orders
     * @throws Exception
     * @throws PDOException
     */
    private function autoCancelOrder()
    {
        $datetime = $this->getDatetime('store_order_wait_time');
        $where    = [['status', 'in', ['1', '2']], ['pay_state', 'eq', '0'], ['create_at', '<', $datetime]];
        $count    = Db::name('StoreOrder')->where($where)->update([
            'status'       => '0',
            'cancel_state' => '1',
            'cancel_at'    => date('Y-m-d H:i:s'),
            'cancel_desc'  => '30分钟未完成支付自动取消订单',
        ]);

        if ($count > 0) {
            $this->output->info("共计自动取消了30分钟未支付的{$count}笔订单！");
        } else {
            $this->output->comment('没有需要自动取消30分钟未支付的订单记录！');
        }
    }

    /**
     * Clean up orders that were not paid a day ago
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    private function autoRemoveOrder()
    {
        $datetime = $this->getDatetime('store_order_clear_time');
        $where    = [['status', 'eq', '0'], ['pay_state', 'eq', '0'], ['create_at', '<', $datetime]];
        $list     = Db::name('StoreOrder')->where($where)->limit(20)->select();

        if (count($orderNos = array_unique(array_column($list, 'order_no'))) > 0) {
            $this->output->info("自动删除前一天已经取消的订单：" . PHP_EOL . join(',' . PHP_EOL, $orderNos));
            Db::name('StoreOrder')->whereIn('order_no', $orderNos)->delete();
            Db::name('StoreOrderList')->whereIn('order_no', $orderNos)->delete();
        } else {
            $this->output->comment('没有需要自动删除前一天已经取消的订单！');
        }
    }

    /**
     * Order automatic refund processing
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    private function autoRefundOrder()
    {
        // For unfinished refund orders, perform WeChat refund operation
        foreach (Db::name('StoreOrder')->where(['refund_state' => '1'])->select() as $order) {
            try {
                $this->output->writeln("正在为 {$order['order_no']} 执行退款操作...");
                $result = We::WePayRefund(config('wechat.wxpay'))->create([
                    'transaction_id' => $order['pay_no'],
                    'out_refund_no'  => $order['refund_no'],
                    'total_fee'      => $order['price_total'] * 100,
                    'refund_fee'     => $order['pay_price'] * 100,
                    'refund_account' => 'REFUND_SOURCE_UNSETTLED_FUNDS',
                ]);

                if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
                    Db::name('StoreOrder')->where(['order_no' => $order['order_no']])->update([
                        'refund_state' => '2', 'refund_desc' => '自动退款成功！',
                    ]);
                } else {
                    Db::name('StoreOrder')->where(['order_no' => $order['order_no']])->update([
                        'refund_desc' => isset($result['err_code_des']) ? $result['err_code_des'] : '自动退款失败',
                    ]);
                }
            } catch (\Exception $e) {
                $this->output->writeln("订单 {$order['order_no']} 执行退款失败，{$e->getMessage()}！");
                Db::name('StoreOrder')->where(['order_no' => $order['order_no']])->update(['refund_desc' => $e->getMessage()]);
            }
        }

        $this->output->writeln('自动检测退款订单执行完成！');
    }

    /**
     * Corporate automatic payment operation
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    private function autoTransfer()
    {
        # Corporate batch payment
        foreach (Db::name('StoreProfitUsed')->where(['status' => '1'])->select() as $vo) {
            try {
                $wechat = We::WePayTransfers(config('wechat.wxpay'));
                $result = $wechat->create([
                    'partner_trade_no' => $vo['trs_no'],
                    'openid'           => $vo['openid'],
                    'check_name'       => 'NO_CHECK',
                    'amount'           => $vo['pay_price'] * 100,
                    'desc'             => '营销活动拥金提现',
                    'spbill_create_ip' => '127.0.0.1',
                ]);

                if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
                    Db::name('StoreProfitUsed')->where(['trs_no' => $vo['trs_no']])->update([
                        'status' => '2', 'pay_desc' => '拥金提现成功！', 'pay_no' => $result['payment_no'], 'pay_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    Db::name('StoreProfitUsed')->where(['trs_no' => $vo['trs_no']])->update([
                        'pay_desc' => isset($result['err_code_des']) ? $result['err_code_des'] : '自动打款失败', 'last_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            } catch (\Exception $e) {
                $this->output->writeln("订单 {$vo['trs_no']} 执行提现失败，{$e->getMessage()}！");
                Db::name('StoreProfitUsed')->where(['trs_no' => $vo['trs_no']])->update(['pay_desc' => $e->getMessage()]);
            }
        }
    }

    /**
     * Get configuration time
     * @param string $code
     * @return string
     * @throws Exception
     * @throws PDOException
     */
    private function getDatetime($code)
    {
        $minutes = intval(sysconf($code) * 60);

        return date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
    }

}
