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

$(function(){$('#cchange').on('click', updateCaptcha)});//Смена каптчи по клику

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
            updateCaptcha()
            $('input[name=captcha]').val('');
            $('html, body').animate({ scrollTop: $('form').offset().top }, 'slow');
            $('#loadbar').addClass('success').html(obj.response);
            $("#loadbar").delay(1000).fadeOut('slow', function () {
                $(this).remove();
            });
        }
        if (obj.code == '403'|| obj.code == '400'){
            updateCaptcha()
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

function updateCaptcha() {
    $c = $('#captchaimage')
    if ($c.length) {
        $c.attr('src', `captcha.php?${new Date().getTime()}`)
    }
    else if (typeof hcaptcha != 'undefined') {
        hcaptcha.reset()
        $('input[type="submit"]').attr('disabled', true)
    }
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

$(function() {
    $('.burger').click(function(ev) {
        ev.stopPropagation()
        $('body').addClass('menu-shown')
    })
    $('.cont-colm').click(function() {
        $('body').removeClass('menu-shown')
    })
})

const socket = {
    init: function() {
        try {
           this.socket = io() 
           return true;
        }
        catch(e) {
            console.error('Ошибка подключения к Socket.IO!')
            return false
        }
    },
    subscribe: function(room, event, callback) {
        if (!this.subscribedTo.includes(room)) {
            this.socket.emit('subscribe', room)
            this.subscribedTo.push(room)
        }
        if (event && callback) {
            this.socket.on(event, data => {
                callback(data)
            })
        }
    },
    subscribedTo: []
}

const entryStats = {
    update: function() {
        if (! this.initiated) {
            socket.socket.on('comment-count', data => {
                let {id, count} = data
                $(`.entry .link[href="/news?id=${id}"] num`).text(count)
            })
            socket.socket.on('rating-update', data => {
                let {id, rating} = data
                $(`.entry .rat-com#${id}`).text(rating)
                .removeClass('red green')
                .addClass(rating < 0 ? 'red' : 'green')
            })
            this.initiated = true
        }
        let sub = [], subTo = this.subscribedTo
        $('.rat-com').each(function() {
            let id = +(this.id)
            if (!isNaN(id) && !subTo.includes(id)) {
                sub.push(`stats:${id}`)
            }
        })
        if (sub.length) {
            socket.subscribe(sub)
            this.subscribedTo.push(...sub)
        }
    },
    initiated: false,
    subscribedTo: []
}

function pushNewsEntry({id, content} = {}) {
    if ($(`.entry .rat-com#${id}`).length) return;
    $('.content').prepend(content)
    entryStats.update()
    setTimeout(() => $(`.entry .rat-com#${id}`).parents('.entry').removeClass('new'), 3000)
    
}

function pushNewComment({id, content} = {}) {
    if ($(`#comment${id}`).length) return;
    $('#createcomm').before(content)
    setTimeout(() => $(`#comment${id}`).removeClass('new'), 3000)
}

function onlineUpdate({was, now} = {}) {
    $('linenum').text(now)
    $('totalnum').text(was)
}

function init_updates() {
    let socketOK = socket.init()
    if (!socketOK) return;
    // Subscribe to updates of the online counter
    socket.subscribe('global', 'online-update', onlineUpdate)
    // Subscribe to new news entries
    if (location.pathname === '/random' && location.search === "") {
        socket.subscribe('global', 'new-entry', pushNewsEntry)
    }
    // Subscribe to new comments
    if (location.pathname == '/news') {
        let entryID = location.search?.match(/id=([0-9]+)/)?.slice(1)
        if (!entryID) return;
        socket.subscribe(`comms:${entryID}`, 'new-comment', pushNewComment)
    }
    // Subscribe to rating and comment count updates
    entryStats.update()
}

function initEmbeds() {
    if (typeof embeds != 'undefined') {
        let siteList = []
        for (let e in embeds) {
            siteList.push(e)
            embeds[e] = new RegExp(embeds[e].substr(1, embeds[e].length-2))
        }
        $('input[name=video]').on('input', function() {
            $('.embed-indicator').remove()
            for (let e in embeds) {
                if (this.value.match(embeds[e])) {
                    $(this).after(`<img class="embed-indicator" src="images/embeds/${e}.png" alt="${e}" title="${e}">`)
                    break
                }
            }
        })
    }
    $('.embed-youtube .et-collapsed').click(function() { // Expand YT video
        $exp = $(this).parents('.expandable-thumb').find('.et-expanded')
        if(!$exp.find('iframe').length) {
            $exp.html(`<iframe src="https://www.youtube.com/embed/${$exp.data('code')}?enablejsapi=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>`)
        }
    })
    $('.embed-youtube .close-button').click(function() { // Pause the video on collapse
        $frame = $(this).parents('.embed-youtube').find('iframe')
        if ($frame.length) {
            $frame[0].contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*')
        }
    })
}

// Used with hcaptcha
function enable_submit() {
    $('input[type="submit"]').attr('disabled', false)
}

$(document).ready(function() {
    init_updates()
     // Simply fucking ping online updates
    setInterval(() => $.get('/api/online.php'), 1000 * 60)

    initEmbeds()
});