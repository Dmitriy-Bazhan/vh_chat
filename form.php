<style>
    .chat-container {
        position: fixed;
        z-index: 10000;
        top: 50%;
        left: 60%;
        height: 45%;
        width: 35%;
        border: solid 1px gray;
        border-radius: 10px;
        background: white;
    }

    .container-with-all-text {
        position: absolute;
        top: 15%;
        left: 0%;
        height: 65%;
        width: 100%;
        overflow: auto;
        background: #36344a;
    }

    .button-hide, .button-show {
        border-radius: 5px;
        position: fixed;
        z-index: 10001;
        top: 51%;
        left: 88%;
        background: #507050;
    }

    .block-with-forms {
        position: absolute;
        top: 80%;
        left: 5%;
        height: 35%;
        width: 90%;
        padding: 5%;
    }

    .message {
        display: inline-block;
        border: black;
        box-shadow: 0 5px 30px rgba(255, 255, 255, 0.1);
        padding: 10px;
        border-radius: 5px;
        background: #706c7d;
        width: 80%;
        height: auto;
        margin: 5% 0 0 5%;
        word-wrap: break-word;
        font-family: 'Helvetica Neue';
        font-size: 1.2em;
        color: rgba(255, 255, 255, 0.7);
    }

    .input-text {
        display: inline;
    }

    .send {
        display: inline;
        padding: 0% 0 0 0;
        bottom: 10%;
    }
</style>

<button class="button-hide">HIDE</button>
<button class="button-show">SHOW</button>
<div class="chat-container">
    <h4>Пример работы с WebSocket</h4>
    <hr>
    <div class="container-with-all-text">

        <div id="message">

        </div>


    </div>
    <div class="block-with-forms">
        <form id="form" action="" name="messages" method="post">
            <input id="user-name" type="hidden" name="user">
            <label for="input-text">Текст: </label>
            <div class="input-text">
                <textarea type="text" id="input-text" name="msg" cols="35" rows="3"></textarea>
            </div>
            <button class="send">Отправить</button>
        </form>
    </div>

    <script>
        (function () {
            $('.button-show').hide();
        })();

        $(document).on('click', '.button-hide', function (event) {
            event.preventDefault();
            $('.chat-container').css({'width': 'auto', 'height': 'auto', 'left': '86%', 'border': 'none'});
            $('.chat-container').hide();
            $('.button-show').show();
            $('.button-hide').hide();

        });

        $(document).on('click', '.button-show', function (event) {
            event.preventDefault();
            $('.chat-container').css({'width': '35%', 'height': '45%', 'left': '60%', 'border': 'solid 1px gray'});
            $('.chat-container').show();
            $('.button-show').hide();
            $('.button-hide').show();

        });

        var socket = new WebSocket("ws://chat:9000");
        var status = '#message';

        socket.onopen = function () {
            let username = document.cookie.split('=')[1];
            $(status).after("cоединение установлено. Здраствуйте " + username + "<br>");

        };

        socket.onclose = function (event) {
            if (event.wasClean) {
                $(status).after('cоединение закрыто');
            } else {
                $(status).after('соединения как-то закрыто');
            }
            $(status).after('<br>код: ' + event.code + ' причина: ' + event.reason + "<br>");
        };

        socket.onmessage = function (event) {
            $(status).after("пришли данные " + event.data);
        };

        socket.onerror = function (event) {
            $(status).after("ошибка " + event.message);
        };
        document.forms["messages"].onsubmit = function () {
            var message = {
                user: this.user.value,
                msg: this.msg.value
            }
            console.log(message);
            socket.send(JSON.stringify(message));
            return false;
        }

        socket.onmessage = function (event) {
            let message = JSON.parse(event.data);
            // let username = document.cookie.split('=')[1];
            let newText = "<div class=\"message\">" + message.user + "<span>: </span>" + message.msg + "</div>";
            $(status).after(newText);
        };
    </script>

</div>