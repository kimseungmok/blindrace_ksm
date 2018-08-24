<LINK REL="SHORTCUT ICON" HREF="./favicon.ico" />
<script
        src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>

    function moveToAnotherPage(where){
        var reqPage = where;

        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/groupsGet')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: null,
            success: function (data) {

                var groupId = data['groups'][0].groupId;

                switch (reqPage){
                    case "recordbox":

                        window.location.href = "{{url('recordbox/chart')}}/" + groupId + "/jp";
                        break;

                    case "feedback":

                        window.location.href = "{{url('recordbox/feedback')}}/" + groupId + "/jp";
                        break;
                }
            },
            error: function (data) {
                alert("로그인부터 해주시기 바랍니다.");
            }
        });
    }

    function tryLogin(){
        var p_id = $('#web_ID').val();
        var p_pw = $('#web_PW').val();
        $.ajax({
            type: 'POST',
            url: "{{url('/userController/webLogin')}}",
            dataType: 'json',
            async:false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data:"p_ID="+p_id+"&p_PW="+p_pw,
            success: function (result) {
                if(result['check'] == true) {

                    switch(result['classification'])
                    {
                        case 'student' :
                            $('#home_race').attr("href", "/race_student");
                            break;
                        case 'teacher' :
                            $('#home_race').attr("href", "/race_list");
                            break;
                    }

                    $('#Login_button').text("Log-Out");
                    $('#Login_button').attr("onclick","tryLogout()");
                    $('.Login_form').hide();
                    window.location.reload();

                }
                else{
                    swal("로그인 실패.", " ", "warning");
                }

            },
            error: function(request, status, error) {

            }
        });
        //ajax끝
    }

    function tryLogout(){
        $.ajax({
            type: 'POST',
            url: "{{url('/userController/webLogout')}}",
            dataType: 'json',
            async:false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (result) {
                $('#Login_button').text("Log-in");
                $('#Login_button').attr("onclick","tryLogin()");

                $('.Login_form').text();
                $('.Login_form').show();

                alert("로그아웃되었습니다.");
                location.href="/";
            },
            error: function(request, status, error) {
                alert("로그아웃 실패 ");
            }
        });
    }
</script>
<style>
    .navbar-brand {
        position: relative;
        z-index: 2;
    }
    .navbar-nav.navbar-right .btn {
        position: relative;
        z-index: 2;
        padding: 4px 20px;
        margin: 10px auto;
    }
    .navbar .navbar-collapse {
        position: relative;
    }
    .navbar .navbar-collapse .navbar-right > li:last-child {
        padding-left: 22px;
    }
    .navbar .nav-collapse {
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: 0;
        padding-right: 120px;
        padding-left: 80px;
        width: 100%;
    }
    .navbar.navbar-default .nav-collapse {
        background-color: black;
        margin: 0;
    }
    .navbar.navbar-inverse .nav-collapse {
        background-color: #222;
    }
    .navbar .nav-collapse .navbar-form {
        border-width: 0;
        box-shadow: none;
    }
    .nav-collapse > li {
        float: right;
    }
    .btn.btn-circle {
        border-radius: 50px;
    }
    .btn.btn-outline {
        background-color: transparent;
    }
    @media screen and (max-width: 767px) {
        .navbar .navbar-collapse .navbar-right > li:last-child {
            padding-left: 15px;
            padding-right: 15px;
        }
        .navbar .nav-collapse {
            margin: 7.5px auto;
            padding: 0;
        }
        .navbar .nav-collapse .navbar-form {
            margin: 0;
        }
        .nav-collapse > li {
            float: none;
        }

    }
    element.style {
        width: 100%;
        height: 60px;
        border-color: transparent;
    }
    .row5, .row5 a {
        color: white;
        background-image: url(https://i.imgur.com/7nT1LDd.png);
        background-size: auto;
    }
    ::-webkit-scrollbar {
        display: none;
    }
    .row5, .row5 a {
    }
    #mainNav{
        width: 100%;
        border-radius: 0 !important;
        height: 53px; !important;
        z-index:50;
    }
    .Login_form{
        background: transparent;
        display: inline-block;
        border: none;
        border-bottom: 2px solid white;
        width: 140px;
        margin-left: 20px;
        margin-top: 10px;
    }
    .Login_form::-webkit-input-placeholder { color: white; }
    #Login_button{
        display: inline-block;
        background-color: transparent;
        border: 2px solid white;
        width: 80px;
        border-radius: 20px;

    }
    /* input box color */
    /*input:-webkit-autofill */
    /*{ }*/
    /*    input:-webkit-autofill, input:-webkit-autofill:hover, */
    /*    input:-webkit-autofill:focus, input:-webkit-autofill:active*/
    /*    { transition: background-color 5000s ease-in-out 0s; }*/

</style>
<div id="mainNav_frame" class="" style="background-color: black">
    <nav id="mainNav" class="navbar  row5" style="margin: 0;width: 100%;">
        <div class="navbar-header">

            <img id="home_logo" src="https://i.imgur.com/dmXfbDm.png" style="width:125px; height:50px; top:0; "/>

        </div>


        <div class=" collapse navbar-collapse" id="navbar-collapse-2" style="position:absolute; right:0;">
            <ul class="nav navbar-nav navbar-right main_navbar">
                <li>
                    <a href="/">Home</a>
                </li>
                <li>
                    <a href="{{ url('mygroup') }}">MyGroup</a>
                </li>
                <li>
                    <a href="{{ url('race_list') }}">Quiz_Race</a>
                </li>
                <li>
                    <a href="#" id="recordbox" onclick="moveToAnotherPage('recordbox')" class="main_navbar_li">RecordBox</a>
                </li>
                <li>

                    <a href="{{ url('quiz_list') }}" >QuizTree</a>
                </li>

                <li style="margin-top:10px;">
                    @if(session()->get('login_check'))
                        <span>@php echo session()->get('user_name'); @endphp</span>
                        <button id="Login_button" type="button" onclick="tryLogout()">Logout</button>
                    @else
                        <input class="Login_form" type="text" name=""
                               id="web_ID"
                               type="text"
                               placeholder="ID"
                               name="p_ID"
                               required="required"

                        />
                        <input class="Login_form" type="text" name=""
                               id="web_PW"
                               type="password"
                               placeholder="Password"
                               name="p_PW"
                               required="required"

                        />
                        <button id="Login_button" type="button" onclick="tryLogin()">Login</button>
                    @endif
                </li>

            </ul>

        </div>

    </nav>
</div>