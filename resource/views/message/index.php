<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>聊天室</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>
    <style>
        .chat {
            padding: 20px 15px;
        }
        .chat-list {
            padding: 0 15px;
        }
        .chat-panel {
            height: 30rem;
            max-height: 30rem;
            width: 100%;
            overflow: auto;
        }
        .chat-item {
            padding: 10px 0;
            border: 1px solid #66afe9;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .chat-user {
            padding-left: 10px;
        }
        .chat-text {
            padding-left: 58px;
        }
        .list-unstyled {
            margin: 0;
        }
        body {
            padding-top: 50px;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="container" id="app">
    <!-- Content here -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand active" href="">聊天室</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="user.html">个人中心</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-xs-3 col-md-4 chat">
                <nav class="bs-docs-sidebar">
                    <div class="panel panel-primary">
                        <div class="panel-heading">全部成员</div>
                        <ul class="list-group">
                            <li class="list-group-item" v-for="(user, index) in userList">{{user}}</li>
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="col-xs-9 col-md-8 chat">
                <div class="chat-list">
                    <div class="chat-panel">
                        <ul class="list-unstyled">
                            <li class="chat-item" v-for="chat in chatList">
                                <div class="chat-user">用户名：{{chat.username}}</div>
                                <div class="chat-text">{{chat.text}}</div>
                            </li>
                            <li v-if="chatList <= 0">
                                <div>暂无聊天内容</div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="chat-send">
                    <div class="chat content-item">
                        <form class="form-group" :class="{'has-error': needContent}">
                            <div class="form-group">
                                <label class="control-label" for="content" v-show="needContent">{{errorText}}</label>
                                <textarea class="form-control" rows="3" @keyup.enter="send" id="content"
                                          v-model="content" @change="checkContent">
                                    {{content}}
                                </textarea>
                            </div>
                            <button type="button" class="btn btn-primary" @click="send">发送</button>
                            <button type="button" class="btn btn-default" @click="clear">清空</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container -->

</div>
<script>
    function ajaxGet(url, data, callback) {
        $.ajax(url, {
            type: 'GET',
            data: data,
            dataType: 'json',
            cache: false,
            success: (res) => {
                if (typeof callback === 'function') {
                    callback(res);
                } else {
                    console.log(res);
                }
            },
            error: (res) => {
                console.log(res);
            }
        });
    }
    function ajaxPost(url, data, callback) {
        $.ajax(url, {
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            success: (res) => {
                if (typeof callback === 'function') {
                    callback(res);
                } else {
                    console.log(res);
                }
            },
            error: (res) => {
                console.log(res);
            }
        });
    }
    function isEmpty(value) {
        value = $.trim(value);
        if (value === '' || value === null || value === undefined) {
            return true;
        }
        return false;
    }

    const vm = new Vue({
        el: '#app',
        data: {
            userList: [],
            content: '',
            needContent: false,
            errorText: '请输入内容',
            chatList: [],
            username: '',
            ws: {},
        },
        mounted() {
            this.getUserList();
            this.opWs();
        },
        created() {
            const username = localStorage.getItem('username');
            if (!username) {
                // window.location.href = 'login.html';
            }
            this.username = username;
        },
        methods: {
            getUserList() {
                ajaxGet('/message/getUserList', '', (res) => {
                    this.userList = res.data;
                })
            },
            send() {
                if (this.checkContent()) {
                    return;
                }
                const data = {
                    cmd: "message.sendToAll",
                    data: this.content,
                    ext: {
                        user_id: 1
                    }
                };
                this.ws.send(JSON.stringify(data));
            },
            clear() {
                this.content = '';
            },
            checkContent() {
                if (isEmpty(this.content)) {
                    this.showError('请输入内容');
                } else {
                    this.needContent = false;
                }
                return this.needContent;
            },
            showError(text) {
                this.needContent = true;
                this.errorText = text;
            },
            opWs() {
                this.ws = new WebSocket("ws://127.0.0.1:18307/chat");
                this.ws.onopen = (evt) => {
                    console.log("已连接");
                };
                this.ws.onmessage = (evt) => {
                    console.log(evt.data);
                    if (evt.data === "1") {
                        console.log("登录成功");
                        return;
                    }
                    const data = {
                        cmd: "message.login",
                        data: evt.data,
                        ext: {
                            user_id: 1
                        }
                    };
                    this.ws.send(JSON.stringify(data));
                };
                this.ws.onclose = (evt) => {
                    console.log('连接中断');
                };
                this.ws.onerror = (evt) => {
                    console.log('发送错误');
                }
            }

        }
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>