
const ws = new WebSocket("ws://127.0.0.1:18307/echo");

ws.onopen = (evt) => {
    console.log("已连接");
};

ws.onmessage = (evt) => {
    console.log( "接收信息：" + evt.data);
    vm.chatList.push(JSON.parse(evt.data));
};

ws.onclose = (evt) => {
    console.log('连接中断');
};

ws.onerror = (evt) => {
    console.log('发送错误');
}