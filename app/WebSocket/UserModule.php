<?php
/**
 * Created by PhpStorm.
 * User: xinxing
 * Date: 2019/8/28
 * Time: 11:09 PM
 */

namespace App\WebSocket;

use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\WebSocket\Server\Annotation\Mapping\OnMessage;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use Swoft\WebSocket\Server\Message\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class UserModule
 *
 * @WsModule()
 * @package App\WebSocket
 */
class UserModule
{

    /**
     * @Inject("redis.pool")
     *
     * @var
     */
    private $redis;

    /**
     *
     *
     * @param Request $request
     * @param int $fd
     */
    public function onOpen(Request $request, int $fd): void
    {
        server()->push($request->getFd(), $fd);
    }

    /**
     * @OnMessage()
     * @param Server $server
     * @param Frame  $frame
     */
    public function onMessage(Server $server, Frame $frame): void
    {
        $uid = $frame->data;
        $fd = $frame->fd;

        $server->push($fd,'登录成功');
    }
}