function inserts(text) {
    var element = document.getElementById("commentText");
    if (document.selection) {
        element.focus();
        var sel = document.selection.createRange();
        sel.text = text;
        element.focus();
    } else if (element.selectionStart || element.selectionStart === 0) {
        var startPos = element.selectionStart;
        var endPos = element.selectionEnd;
        var scrollTop = element.scrollTop;
        element.value = element.value.substring(0, startPos) + text + element.value.substring(endPos, element.value.length);
        element.focus();
        element.selectionStart = startPos + text.length;
        element.selectionEnd = startPos + text.length;
        element.scrollTop = scrollTop;
    } else {
        element.value += text;
        element.focus();
    }
}

function insert(text) {
    inserts(text+'\n')
}

function highlight(post) {
    var cells = document.getElementsByTagName("table");
    for (var i = 0; i < cells.length; i++) if (cells[i].className == "comment highlight") cells[i].className = "comment";

    var comment = document.getElementById("comment" + post);
    if (comment) {
        comment.className = "comment highlight";
        var match = /^([^#]*)/.exec(document.location.toString());
        document.location = match[1] + "#" + post;
    }
}

window.onload = function () {
    if (match = /#([0-9]+)/.exec(document.location.toString())) highlight(match[1]);
}

function youtube(idvideo) {
    $('#' + idvideo).replaceWith('<iframe width="360" height="228" style="vertical-align:top;" src="https://www.youtube.com/embed/' + idvideo + '?autoplay=1" frameborder="0" allowfullscreen></iframe>');
}

function v(i) {
    if (i > 9) return i;
    else return "0" + i
}

function timenow() {
    var now = new Date();
    var week = Array("SOSкресение", "Понедельник", "Вторник", "Среда", "Четверг", "Девчатница", "Субкота");
    $('#timeto').html(week[now.getDay()] + ", " + v(now.getDate()) + "." + v(now.getMonth() + 1) + "." + now.getFullYear() + " " + v(now.getHours()) + ":" + v(now.getMinutes()) + ":" + v(now.getSeconds()) + "&nbsp;");
}
setInterval(timenow, 100);

function vote(method, id) {
    $('#loadbar').remove();
    $("body").append('<div id="loadbar" class="pr pr-text">Загрузка...</div>');
    $.ajax({
        type: "GET",
        url: '/rate.php?id=' + id + '&method=' + method + '&lastthread=' + $('span[class="info"] span').eq(1).attr('id') + '&posts=' + $('.info').length,
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            $('#' + id).fadeIn(500).html(obj.num);
            if (obj.response == '200') {
                $('#loadbar').addClass('success').html(obj.message);
                $("#loadbar").delay(2000).fadeOut('slow', function () {
                    $(this).remove();
                });

            } else if (obj.response == '100') {
                $("#loadbar").addClass("error").html(obj.message);
                $("#loadbar").delay(2000).fadeOut('slow', function () {
                    $(this).remove();
                });
            } else {
                $("#loadbar").addClass("error").html(obj.message);
                $("#loadbar").delay(2000).fadeOut('slow', function () {
                    $(this).remove();
                });
            }
        }
    });
    return false;
}

$(function(){$('#reload').on('click',function(){location.reload()})});//Обновление страницы по кнопочке, той, что рядом с часами

$(function(){$('#cchange').on('click',function(){$("#captchaimage").attr("src","captcha.php?" + new Date().getTime());return false})});//Смена каптчи по клику

$(function () {/*AJAX-постинг новости*/
    $("form#createnews").submit(function (event) {
        event.preventDefault();
        $('#loadbar').remove();
        $("body").append('<div id="loadbar" class="pr pr-text">Загрузка...</div>');
        $.post('api/nnews.php', $('#createnews').serialize(), function (data) {
            var obj = jQuery.parseJSON(data);
            if (obj.code == '200') {
                $('#loadbar').addClass('success').html(obj.response);
                $("#loadbar").delay(1000).fadeOut('slow', function () {
                    $(this).remove();
                    window.location = "/news?id=" + obj.id;
                });
            } else {
                //noinspection JSJQueryEfficiency
                $("#loadbar").addClass("error").html(obj.response);
                $("#loadbar").delay(2000).fadeOut('slow', function () {
                    $(this).remove();
                });
            }
        });
        return false;
    });
});

function createcomm() {/*AJAX-добавление комментариев */
    $('#loadbar').remove();
    $("body").append('<div id="loadbar" class="pr pr-text">Загрузка...</div>');
    $.post('api/ncomm.php', $('#createcomm').serialize(), function (data) {
        var obj = jQuery.parseJSON(data);
        if (obj.code == '200') {
            $("#commentText").val('');
            $('captchazone').html('<a id="cchange"><img src="captcha.php?' + new Date().getTime() + '" id="captchaimage"></a><input type="text" name="captcha" autocomplete="off">');
            $('input[name=captcha]').val('');
            $('html, body').animate({ scrollTop: $('form').offset().top }, 'slow');
            $('#loadbar').addClass('success').html(obj.response);
            $("#loadbar").delay(1000).fadeOut('slow', function () {
                $(this).remove();
            });
        }
        if (obj.code == '403'|| obj.code == '400'){
            $('captchazone').html('<a id="cchange"><img src="captcha.php?' + new Date().getTime() + '" id="captchaimage"></a><input type="text" name="captcha" autocomplete="off">');
            $("#loadbar").addClass("error").html(obj.response);
            $("#loadbar").delay(2000).fadeOut('slow', function () {
                $(this).remove();
            });

        }    else {
            $("#loadbar").addClass("error").html(obj.response);
            $("#loadbar").delay(2000).fadeOut('slow', function () {
                $(this).remove();
            });
        }
    });
}

$(function () { /*AJAX-постинг комментариев*/
    $('#commentText').keydown(function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            createcomm();
        }
    });
    $("form#createcomm").submit(function (event) {
        event.preventDefault();
        createcomm();
        return false;
    });
});

function getnews() {
    if (location.pathname == '/random' && location.search == "") {
        var lastid = $('a[class="link"]')[0].href.substring($('a[class="link"]')[0].href.lastIndexOf('=') + 1);
        $.ajax({
            type: "GET",
            url: "api/nupdate.php",
            data: {
                "lastid": lastid
            },
            success: function (data) {
                if (data.substr(0, 1) == '{') {
                    lastid = data.lastid;
                } else {
                    $('.content').prepend(data);
                    setTimeout(function() {
                            $('.entry').removeClass('new');
                        },
                        3000);
                }
                setTimeout("getnews()", 1000)
            }
        })
    }
}

function getcomms() {
    if (location.pathname == '/news') {
        var thread = $('#enty').attr('value'),
            lastid = $('id').attr('id')

        $.ajax({
            type: "GET",
            url: "/api/cupdate.php",
            data: {
                "id": thread,
                "lastid": lastid
            },
            success: function (data) {
                if (data.substr(0, 1) == '{') {
                    lastid = data.lastid;
                } else {
                    $('id').after(data);
                    $('count').remove();
                    $('id[id="' + lastid + '"]').remove();
                    setTimeout(function () {
                            $('.comment').removeClass('new');
                        },
                        3000);
                }
                setTimeout("getcomms()", 1000)
            }
        })
    }
}

function getonline() {
    $.ajax({
        type: "GET",
        url: "/api/oupdate.php",
        data: {
            "online": $('linenum').html(),
            "was":$('totalnum').html()
        },
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            $('linenum').html(obj.online);
            $('totalnum').html(obj.was);
            setTimeout("getonline()", 1000);
        }
    })
}

/*function initplexor() {
    var rateCommUpd = function (result, id, cursor) {
        var obj = result;
        if (obj['rate']) {
            $('#' + obj['rate']['id']).fadeOut('slow', function () {
                $(this).fadeIn(100).removeClass('green').removeClass('red').addClass(obj['rate']['resonance']).html(obj['rate']['rating']);
            });
        }
        if (obj['comm']) {
            $('#' + obj['comm']['id']).parent().find('num').parent().addClass('discach');
            setTimeout(function () {
                $('#' + obj['comm']['id']).parent().find('num').parent().removeClass('discach');
            }, 3000);
            $('#' + obj['comm']['id']).parent().find('num').fadeIn('slow').html(obj['comm']['num']);
        }
    };

    var realplexor = $.Realplexor({
        url: 'https://psh.lentachan.ru',
        namespace: 'main'
    }) .setCursor('updater', 0).subscribe('updater', rateCommUpd).execute();
}*/

$(document).ready(function() {
    getnews();
    getcomms();
    getonline();
});