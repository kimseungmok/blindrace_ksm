
<div class="homework" style="float: left;overflow-y: scroll;">
    {{--해야할 과제 목록 보기--}}
    <div class="raceListDetail" style="width: 500px;">
        <table class="table table-hover table-bordered table-striped" style="height: 0;width: 100%;" >
            <thead>
            <tr>
                <th width="50px">
                    번호
                </th>
                <th>
                    학생
                </th>
                <th width="110px">
                    재시험
                </th>
                <th width="110px">
                    오답노트
                </th>
            </tr>
            </thead>

            {{--getStudent()로 학생들 불러오기--}}
            <tbody>
            <tr>
                <td width="50px">
                    1
                </td>
                <td>
                    스쿠스쿠1
                </td>
                <td width="90px">
                    <button class="btn btn-warning"> 미응시</button>

                </td>
                <td width="90px">
                    <button class="btn btn-primary"> 제출</button>
                </td>
            </tr>
            <tr>
                <td width="50px">
                    2
                </td>
                <td>
                    집중공략1
                </td>
                <td width="90px">
                    <button class="btn btn-warning"> 미응시</button>

                </td>
                <td width="90px">
                    <button class="btn btn-warning"> 미제출</button>

                </td>
            </tr>

            </tbody>

        </table>
    </div>
</div>