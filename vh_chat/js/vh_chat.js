window.onload = function () {
    (function () {
        var token;
        new Promise(function (resolve) {
            $.post("vh_chat/auth", function (data) {
                let dataWithoutSpace = data.replace(/\s+/g, '');
                token = dataWithoutSpace.split('=')[1];
                let block = '<input id="token" type="hidden" name="token" value="' + token + '">'
                $('#vh_chat').append(block);
                resolve(token);
            });
        }).then(function (token) {
            $.ajax({
                method: 'post',
                url: 'http://chat/auth',
                dataType: 'json',
                data: {
                    id: token
                },
                success: function (data) {
                    var resultDataAjax;
                    if (data) {
                        resultDataAjax = $.parseJSON(data);
                        document.cookie = "username=" + resultDataAjax.name;
                        if (resultDataAjax.message == 'Enabled') {
                            $.post("vh_chat/form.php", function (data) {
                                $("#vh_chat").append(data);
                                $("#user-name").val(resultDataAjax.name);
                                addCommentsToChat();
                            });
                        } else {
                            console.log('Permission denied or chat_api_id not right');
                        }
                    } else {
                        console.log('Permission denied or chat_api_id not right');
                    }
                }, error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        });
    })();

    function addCommentsToChat() {
        $.ajax({
            method: 'post',
            url: 'http://chat/add_comments',
            dataType: 'json',
            success: function (data) {
                for (let i = data.length - 1; i => 0; i--) {
                    // let newText = "<div class=\"message\">" + data[i].user_name + "<span>: </span>" + data[i].comment + "</div>";
                    let newText = "<br>&nbsp;<span class=\"connect-message\">" + data[i].user_name + "<span><br><div class=\"message\">"
                        + data[i].comment + "</div><hr>";
                    $('.input-old-messages').after(newText);
                }
            }
            ,
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    }
}