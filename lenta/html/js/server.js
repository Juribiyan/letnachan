var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var users_num = 0; //текущий онлайн
var ip_array = new Array(); //массив вида 'ip' => кол-во коннектов с браузера

function contains_ip(array, ip) {
    for (var i = 0; i < array.length; i++) {
        if (array[i].ip === ip) {
            return i;
        }
    }
    return false;
}

io.on('connection', function(socket){
    var user_ip = socket.request.connection.remoteAddress;
    var check = contains_ip(ip_array, user_ip);
    if(check === false) { //именно тройное равенство, ибо contains_ip может вернуть индекс 0, а он == false
        var person = [];
        person["ip"] = user_ip;
        person["cn"] = 1; // cn - число коннектов
        check = ip_array.push(person) - 1;
        users_num++;
        io.emit('users num', users_num);
    } else {
        ip_array[check].cn += 1;
    }
    socket.on('disconnect', function(){
        if(ip_array[check].cn - 1 < 1) {
            users_num--;
            io.emit('users num', users_num);
        } else {
            ip_array[check].cn -= 1;
        }

    });
});

http.listen(3000, function(){
    console.log('listening on *:3000');
});