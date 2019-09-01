<?php declare(strict_types=1);

namespace App\WebSocket;

use App\WebSocket\Chat\MessageController;
use Swoft\Http\Message\Request;
use Swoft\Log\Helper\CLog;
use Swoft\Server\Server;
use Swoft\WebSocket\Server\Annotation\Mapping\OnClose;
use Swoft\WebSocket\Server\Annotation\Mapping\OnOpen;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use Swoft\WebSocket\Server\MessageParser\JsonParser;
use function server;

/**
 * Class ChatModule
 *
 * @WsModule(
 *     "/chat",
 *     messageParser=JsonParser::class,
 *     controllers={MessageController::class}
 * )
 */
class ChatModule
{
    /**
     * @OnOpen()
     * @param Request $request
     * @param int $fd
     */
    public function onOpen(Request $request, int $fd): void
    {
        server()->push($request->getFd(), (string)$fd);
    }

    /**
     * @OnClose()
     *
     * @param Server $server
     * @param int $fd
     */
    public function onClose(Server $server, int $fd): void
    {
        CLog::info("用户退出：" . $fd);
    }
}
