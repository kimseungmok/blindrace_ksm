<!doctype html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        input[type=radio] {
            display:none;
        }

        input[type=radio] + label {
            display:inline-block;
            margin:-2px;
            padding: 4px 12px;
            margin-bottom: 0;
            font-size: 14px;
            line-height: 20px;
            color: #333;
            text-align: center;
            text-shadow: 0 1px 1px rgba(255,255,255,0.75);
            vertical-align: middle;
            cursor: pointer;
            background-color: #f5f5f5;
            background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
            background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
            background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
            background-image: -o-linear-gradient(top,#fff,#e6e6e6);
            background-image: linear-gradient(to bottom,#fff,#e6e6e6);
            background-repeat: repeat-x;
            border: 1px solid #ccc;
            border-color: #e6e6e6 #e6e6e6 #bfbfbf;
            border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
            border-bottom-color: #b3b3b3;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#ffe6e6e6',GradientType=0);
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
            -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
        }

        input[type=radio]:checked + label {
            background-image: none;
            outline: 0;
            -webkit-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
            -moz-box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.15),0 1px 2px rgba(0,0,0,0.05);
            background-color:#e0e0e0;
        }
    </style>
</head>

<body id="client">

<script src="//code.jquery.com/jquery-1.11.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.13/vue.min.js"></script>
<div id="app">
</div>
<script src="{{asset('js/app.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
<script>
    window.onload = function () {
        var socket = io(':8890'); //1

        var room_num = '';
        socket.on('room_num', function(data){
            alert('참여완료');
        });
        
         socket.emit('join','Name');   
        
        
        socket.on('message', function(data){
           $('<p>' + data + '</p>').appendTo('body');
        });

        socket.on('answer-sum', function(data){
           document.getElementById('answer_c').innerText= data;
        });

        document.getElementById('sub').onclick = function (){
            var text = $(':input[name=answer]:radio:checked').val();
            socket.emit('answer' , text);
        };
    };


</script>
<form id="answer" >

    <p>Answers :</p>
    <p id="answer_c"></p>

    <div>
        <input class="btn-info" type="radio" id="contactChoice1" name="answer" value="1">
        <label for="contactChoice1">1번답안</label>

        <input class="btn-info" type="radio" id="contactChoice2" name="answer" value="2">
        <label for="contactChoice2">2번답안</label>

        <input class="btn-info" type="radio" id="contactChoice3" name="answer" value="3">
        <label for="contactChoice3">3번답안</label>

        <input class="btn-info" type="radio" id="contactChoice4" name="answer" value="4">
        <label for="contactChoice4">4번답안</label>
    </div>
    <br>
    <div>
        <input id="sub" type="button" value="answer">
    </div>
</form>
</div>
// function checkAnimal(){
//     var answer = $(':input[name=answer]:radio:checked').val();
//
//     if( answer ){
//
//     }else{
//         alert("답을 선택하세요");
//
//     }
// }
</body>
</html>
