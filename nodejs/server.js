
var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);

// Mysql 노드 모듈 부분
var mysql      = require('mysql');
var dbconfig   = require('./config/database.js');
var connection = mysql.createConnection(dbconfig);

//소켓아이오 -------------------------------------------------------------------
server.listen(8890);


//소켓io 연결 비연결 !
io.on('connection',function(socket){
    console.log('a user connected');

    socket.on('disconnect',function(){
        console.log('a user disconnected');
    });
});

// ---------------------------------------------- 연결처리작업
//changes

io.on('connection', function (socket){
    var Timer ;
    var countdown = 30000;

    //대기방 참가 (인수 room : 참가하려는 방의 이름 )
    socket.on('join',function (room) {
        socket.join(room);
        console.log('join',room);
    });


    //웹에서 학생이 입장할 때 사용하는 함수
    socket.on('web_test_enter',function(roomPin){
        io.sockets.emit('web_test_enter',roomPin);
    });



    socket.on('android_join',function(roomPin , sessionId){
        io.sockets.emit('android_join',roomPin,sessionId);
        console.log('안드조인',roomPin+","+sessionId);
    });

    //방에 입장하려할 때 핀번호를 검사하기위해 쓰는 함수 (true , false): (입장 ,입장실패)
    socket.on('android_join_check',function(join_boolean , sessionId ,raceType,character_info){
        console.log(join_boolean+","+character_info);
        io.sockets.emit('android_join_result',join_boolean,sessionId , raceType , character_info);
    });


    // 대기방에서 이탈하는 경우 실행되는 함수
    socket.on('leaveRoom', function( group_num, user_num){
        io.sockets.in(group_num).emit('leaveRoom',user_num);
        console.log('danger', group_num+","+user_num);
    });

    //대기방 인원 입장시 실행되는 함수
    socket.on('user_in',function(pin,nickname,session_id,character_num){
        console.log('유저참가', '핀번호:'+pin+'등록번호:'+session_id+'닉네임'+nickname+'캐릭터번호:'+character_num);
        io.sockets.in(pin).emit('user_in',pin,nickname,session_id,character_num);
    });
    //쪽지시험 타이머
    socket.on('pop_timer',function (roomPin,realTime) {
        io.sockets.in(roomPin).emit('pop_timer',realTime);
    })

    //쪽지시험 시작을 동시에 할 수 있게 해주는 함수
    socket.on('pop_quiz_start',function(roomPin,quizData,listName,sessionId,quizCount){
        console.log('PopQuiz시작',roomPin+","+quizData)
        io.sockets.in(roomPin).emit('pop_quiz_start',quizData,listName,sessionId,quizCount);
    });

    //쪽지시험이 끝났음을 알리는 함수
    socket.on('pop_quiz_status',function(roomPin,sessionId){
        console.log('쪽지시험 끝남 ++');
        io.sockets.in(roomPin).emit('pop_quiz_status',sessionId);
    });

    //대기방에서 퇴장한 유저의 캐릭터를 다시 활성화 시키는 함수
    socket.on('enable_character',function(roomPin,char_num){
        io.sockets.in(roomPin).emit('enable_character',char_num);
        console.log("방이탈 "+roomPin+","+char_num)
    });


    //웹 학생 접속성공여부
    socket.on('web_enter_room',function(roomPin,listName,quizCount,groupName,groupStudentCount, sessionId,enter_check){
        io.sockets.in(roomPin).emit('web_enter_room',listName,quizCount,groupName,groupStudentCount, sessionId,enter_check);
    });

    //안드로이드에서 레이스를 시작시키는 함수
    socket.on('android_game_start',function(roomPin, quizId ,makeType){
        io.sockets.in(roomPin).emit('android_game_start', quizId , makeType);
        console.log("안드스타트",quizId+","+makeType);
    });

    //안드로이드에서 중간결과를 받게 하는 함수
    socket.on('android_mid_result',function(roomPin, quizId ,makeType ,ranking ){
        io.sockets.in(roomPin).emit('android_mid_result',quizId, makeType, ranking);
        console.log("안드 중간결과 ", quizId +","+ makeType+","+ranking);
    });

    //레이스 중간결과에서 정답정보를 따로 보내주는 함수
    socket.on('race_mid_correct',function(roomPin,correct){
        io.sockets.in(roomPin).emit('race_mid_correct',correct);
    });

    //다음 문제로 넘어가게 하는 함수
    socket.on('android_next_quiz',function(roomPin){
        io.sockets.in(roomPin).emit('android_next_quiz',roomPin);
        console.log("안드로이드 다음문제 " );
    });

    //안드로이드에서 Pin번호를 체크받게 하는 함수
    socket.on('android_enter_room',function(roomPin , check , session_id){
        io.sockets.in(roomPin).emit('android_enter_room',roomPin,check,session_id);
    });


    // 타이머 시작함수
    socket.on('count',function(data,group_num,makeType){

        //시간초 반복작업부분
        //Timer라는 변수에 시간초 반복을 담아두고
        //퀴즈가 끝날 때 이함수를 중지하게 만든다.
        Timer = setInterval(function () {
            countdown -= 1000;
            io.sockets.in(group_num).emit('timer',countdown);
        }, 1000);
        console.log('타임온',group_num);

        //시간초가 다되었을 시 다음 문제로 갈 수 있도록 함
        if( data == '1'){
            io.sockets.in(group_num).emit('nextok',0,makeType);
        }
    });


    //다음문제로 넘어가기전 Timer를 취소하는 함수
    socket.on('count_off', function(quiz , roomPin , makeType){
        console.log('group_num',roomPin)

        //시간초 변수값을 돌리고 시간초를 중지한다.
        countdown = 30000;
        clearInterval(Timer);

        io.sockets.in(roomPin).emit('mid_ranking' ,quiz);
        console.log("퀴즈타입",makeType+","+quiz);
        io.sockets.in(roomPin).emit('nextok',quiz ,makeType);
    });


    //퀴즈 답받는 소켓 함수
    socket.on('answer', function(roomPin , answer_num , student_num , nickname , quizId){

        io.sockets.in(roomPin).emit('answer-sum',answer_num,student_num ,quizId);
        console.log("답한 퀴즈  =",quizId+":"+answer_num);
    });

    //레이스가 끝난 것을 알려주는 함수
    socket.on('race_ending',function(roomPin){
        clearInterval(Timer);
        io.sockets.in(roomPin).emit('race_ending',roomPin);
    });

    //레이스 결과 정보들을 배열로 전송하는 함수
    socket.on('race_result',function(roomPin, race_result){
        io.sockets.in(roomPin).emit('race_result',race_result);
    });

});

server.listen(8890, function(){ //4
    console.log('server on!');
});











