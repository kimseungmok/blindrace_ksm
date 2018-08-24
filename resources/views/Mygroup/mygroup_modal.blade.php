<!DOCTYPE html>

<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>YourChoice</title>
    <meta charset="utf-8">

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <style>
        .btn-primary-outline {
            background-color: transparent;
            border-color: #ccc;
        }
        .btn-round-lg{
            border-radius: 20.5px;

        }

        button {
            display: inline-block;
        }

        .modal-content {
            position: relative;
            background-color: #D5C5E6;
            margin: auto;
            padding: 0;
            border: 5px solid #D5C5E6; !important;
            width: 80%;

        }
        p, div, th, tr, td, button, a {
            font-family: 'Meiryo UI' ; !important;
        }

        ul {
            font-family: 'Meiryo UI'; !important;
        }

    </style>
    <!-- careate Modal -->

    <div
            class="modal fade"
            id="create"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <label for="createCl">
                    <b>クラス作り</b>
                </label>

                <input id="groupNameValue" type="text"
                       placeholder="クラスの名前"
                       name="groupName"
                       required="required"
                       style="margin-left: 30px"

                >
                <button class="btn btn-primary-outline btn-round-lg" onclick="createGroup()" style ="color : black; margin-left: 85px">クラス作り</button>

            </div>


        </div>


        <!-- teacher modal -->
        <div
                class="modal fade"
                id="teacher"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleDropdownFormEmail1">교사 이름</label>
                            <input
                                    type="email"
                                    class="form-control"
                                    id="exampleDropdownFormEmail1"
                                    placeholder="김민수">
                        </div>
                        <div>
                            <p>현재 클래스</p>
                            <select>
                                <option value="volvo">Volvo</option>
                                <option value="saab">Saab</option>
                                <option value="opel">Opel</option>
                                <option value="audi">Audi</option>
                            </select>
                            <p>이동할 클래스</p>
                            <select>
                                <option value="volvo">Volvo</option>
                                <option value="saab">Saab</option>
                                <option value="opel">Opel</option>
                                <option value="audi">Audi</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 초대 modal -->
        <div
                class="modal fade"
                id="exampleModal"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ... 선생님의 클래스에 등록하세요 초대코드 00000을 입력하세요
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">복사하기</button>
                    </div>
                </div>
            </div>

        </div>
        <!-- 학생 modal -->
        <div
                class="modal fade"
                id="studnetsetting"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLongTitle"
                aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="px-4 py-3">
                        {{--<div class="form-group">--}}
                        {{--<label for="exampleDropdownFormEmail1">이름</label>--}}
                        {{--<input--}}
                        {{--type="email"--}}
                        {{--class="form-control"--}}
                        {{--id="exampleDropdownFormEmail1"--}}
                        {{--placeholder="김민수">--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                        {{--<label for="exampleDropdownFormEmail1">학번</label>--}}
                        {{--<input--}}
                        {{--type="email"--}}
                        {{--class="form-control"--}}
                        {{--id="exampleDropdownFormEmail1"--}}
                        {{--placeholder="1301036">--}}
                        {{--</div>--}}
                        <div class="form-group">
                            <label for="exampleDropdownFormPassword1">비밀번호</label>
                            <input
                                    type="password"
                                    class="form-control"
                                    id="exampleDropdownFormPassword1"
                                    placeholder="1401036">
                        </div>

                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script>

    /////클래스 생성 ////////
        function createGroup() {
            var groupNameValue = document.getElementById("groupNameValue").value;
            $.ajax({
                type: 'POST',
                url: "{{url('/groupController/createGroup')}}",
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: "groupName=" + groupNameValue,
                success: function (data) {
                    if (data['check'])
                        swal('成功的にクラスが作られました。');

                    setTimeout(function(){ window.location.href = "{{url('mygroup')}}"; }, 1200);

                },
                error: function (data) {
                    alert("クラス名を入力してください");
                }
            });
        }


    </script>