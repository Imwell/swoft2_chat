<?php
/**
 * Created by PhpStorm.
 * User: xinxing
 * Date: 2019/8/26
 * Time: 8:10 PM
 */

namespace App\Http\Controller;

use Swoft\Context\Context;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\View\Renderer;

/**
 * Class MessageController
 *
 * @Controller("message")
 *
 * @package App\Http\Controller
 */
class MessageController
{

    /**
     *
     * @RequestMapping("index")
     *
     * @return \Swoft\Http\Message\Response|\Swoft\WebSocket\Server\Message\Response
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Throwable
     */
    public function index()
    {

        /** @var Renderer $renderer */
        $renderer = \Swoft::getBean('view');
        $content = $renderer->render('message/index');
        return Context::mustGet()
            ->getResponse()
            ->withContentType(ContentType::HTML)
            ->withContent($content);
    }

    /**
     * @RequestMapping("getUserList")
     *
     */
    public function getUserList()
    {
        $response = Context::mustGet()->getResponse();
        $data = [
            'data' => ['xx', 'cc']
        ];
        return $response->withData($data)->withContentType("application/json");
    }
}