<style>
    .chat-container {
        position: fixed;
        z-index: 10000;
        top: 10%;
        left: 15%;
        height: 80%;
        width: 70%;
        border: solid 1px gray;
        border-radius: 10px;
        background: #36344a;
    }

    .container-with-all-text {
        height: auto;
        width: 100%;
        overflow: auto;
        background: #36344a;
    }

    ::-webkit-scrollbar {
        width: 12px; /* ширина для вертикального скролла */
        height: 48px; /* высота для горизонтального скролла */
        background-color: #36344a;
    }

    ::-webkit-scrollbar-thumb {
        background-color: #706c7d;
        border-radius: 9em;
        box-shadow: inset 1px 1px 10px #f3faf7;
    }

    .button-hide, .button-show {
        border-radius: 5px;
        position: fixed;
        z-index: 10001;
        top: 90%;
        left: 90%;
        background: #706c7d;
    }

    .block-with-forms {
        position: absolute;
        top: 77%;
        left: 0%;
        height: auto%;
        width: 100%;
        padding: 5%;
    }

    .message {
        display: inline-block;
        border: black;
        /*box-shadow: 0 5px 30px rgba(255, 255, 255, 0.1);*/
        padding: 10px;
        border-radius: 5px;
        /*background: #706c7d;*/
        width: 85%;
        height: auto;
        margin: 5% 0 0 5%;
        word-wrap: break-word;
        font-family: 'Helvetica Neue';
        font-size: 1.2em;
        color: rgba(255, 255, 255, 0.7);
    }

    #input-text {
        border-radius: 10px;
        border: solid 1px #706c7d;
        width: 55%;
        /*height: 100px;*/
        /*max-height: 40px;*/
        margin-bottom: 5%;
        overflow: auto;
        font-size: 1em;
        color: rgba(255, 255, 255, 0.7);
    }

    #input-text:focus {
        outline: none;
    }

    .send {
        border-radius: 10px;
        border: solid 1px #706c7d;
        background: #706c7d;
        padding: 5px 10px 5px 10px;
        bottom: 10%;
        font-size: 1em;
        color: rgba(255, 255, 255, 0.7);
    }

    .connect-message {
        color: limegreen;
    }

    .connect-close {
        color: red;
    }

    h4 {
        color: yellow;
        margin: 2% 0 0 5%;
    }

    hr {
        height: 10px;
    }
</style>

<button class="button-hide">HIDE</button>
<button class="button-show">SHOW</button>
<div class="chat-container">
    <h4>VH CHAT</h4>
    <hr>

    <div class="container-fluid">
        <div class="row">
            <div class="col-3">

            </div>

            <div class="col-9">
                <div class="container-with-all-text">
                    <div id="message">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block-with-forms">
        <form id="form" action="" name="messages" method="post">
            <input id="user-name" type="hidden" name="user">
            <div class="container">
                <div class="row">
                    <div class="col-8" id="input-text" contenteditable="true"></div>
                    <div class="offset-1 col-3">
                        <button class="send">Send</button>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script>
        var secret_token = $('#token').val();
        var socket = new WebSocket("ws://chat:9000?secret_token=" + secret_token);
        var status = '#message';

        socket.onopen = function (data) {
            let username = getCookie('username');
            $(status).after("<br><div class=\"message input-old-messages\"><span class=\"connect-message\">&nbsp;Соединение установлено. Здраствуйте " + username + "</span></div>");
            keepAlive();
        };

        socket.onclose = function (event) {
            if (event.wasClean) {
                $(status).after('Соединение закрыто');
            } else {
                $(status).after('Соединения как-то закрыто');
            }
            $(status).after('<br><div class=\"message\"><span class="connect-close">код: ' + event.code + ' причина: ' + event.reason + "<span></div><br>");
            cancelKeepAlive();
        };

        socket.onerror = function (event) {
            $(status).after("ошибка " + event.message);
            cancelKeepAlive();
        };

        document.forms["messages"].onsubmit = function () {
            var message = {
                user: this.user.value,
                msg: $('#input-text').text(),
                token: $('#token').val(),
            }
            $('#input-text').text(''),
                socket.send(JSON.stringify(message));
            return false;
        }

        socket.onmessage = function (event) {
            let message = JSON.parse(event.data);
            console.log(message);
            let newText = "&nbsp;<span class=\"connect-message\">" + message.user + "<span><br><div class=\"message\">" + message.msg + "</div><hr>";
            $(status).after(newText);
        };

        function getCookie(name) {
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }

        (function () {
            $('.button-show').hide();
            let height = $('.chat-container').height();
            $('.container-with-all-text').height(height * 0.65);
            $('#input-text').height(height * 0.1);
        })();

        $(document).on('click', '.button-hide', function (event) {
            event.preventDefault();
            $('.chat-container').hide();
            $('.button-show').show();
            $('.button-hide').hide();
        });

        $(document).on('click', '.button-show', function (event) {
            event.preventDefault();
            $('.chat-container').show();
            $('.button-show').hide();
            $('.button-hide').show();
        });

        var connectAliveTimer = 0;

        function keepAlive() {
            var timeout = 120000;
            if (socket.readyState == socket.OPEN) {
                let msg = {
                    token: $('#token').val(),
                };
                socket.send(JSON.stringify(msg));
            }
            connectAliveTimer = setTimeout(keepAlive, timeout);
        }

        function cancelKeepAlive() {
            if (connectAliveTimer) {
                clearTimeout(connectAliveTimer);
            }
        }

        $(window).resize(function () {
            let height = $('.chat-container').height();
            $('.container-with-all-text').height(height * 0.65);
            $('#input-text').height(height * 0.1);
        });

    </script>

</div>