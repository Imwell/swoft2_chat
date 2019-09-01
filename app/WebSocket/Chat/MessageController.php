<?php
/**
 * Created by PhpStorm.
 * User: xinxing
 * Date: 2019/8/28
 * Time: 10:00 PM
 */

namespace App\WebSocket\Chat;

use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Log\Helper\CLog;
use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;

/**
 * Class MessageController
 *
 * @WsController()
 * @package App\WebSocket\Chat
 */
class MessageController
{

    /**
     *
     * @Inject("redis.pool")
     *
     * @var
     */
    private $redis;

    /**
     *
     * Message command is: 'message.sendToOne'
     * 个人发送
     *
     * @param $data
     * @MessageMapping()
     */
    public function sendToOne($data): void
    {
        $data = json_decode($data, true);
        $user_id = $data['ext']['user_id'];
        $msg = $data['data'];
        $user = $this->redis->get("USER_LOGIN:" . $user_id);
        $user = json_decode($user, true);
        if (isset($user) && $user['fd']) {
            server()->sendTo($user['fd'], $msg);
        }
    }

    /**
     * Message command is: 'message.sendToAll'
     * 所有人广播
     *
     * @param $data
     * @MessageMapping()
     */
    public function sendToAll($data)
    {
        $msg = $data['data'];
        server()->sendToAll($msg);
    }

    /**
     * Message command is: 'message.login'
     * @param $data
     * @MessageMapping(command="login")
     */
    public function login($data)
    {
        $data = json_decode($data, true);
        $fd = $data['data'];
        $uid = $data['ext']['user_id'];
        $data = [
            'fd' => $fd,
            'status' => 1
        ];
        $this->redis->set("USER_LOGIN:" . $uid, json_encode($data));
        server()->sendTo($fd, 1);
    }

}