<?php
namespace app\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
class GroupController extends Controller{

    /****
     * 그룹 목록 가져오기
     *
     * @param Request $request->input()
     *      ['sessionId'] 모바일용 변수
     *
     * @return array(
     *      'groups'    => array(
     *          0 => array(
     *                  'groupId' 그룹 아이디
     *                  'groupName' 그룹 이름
     *                  'retestStateCount' 재시험 미제출 학생 수
     *                  'wrongStateCount' 오답노트 미제출 학생 수
     *              )
     *          ),
     *          'check' 조회 성공 여부
     *      )
     */
    public function groupsGet(Request $request){
        $postData = array(
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 유저 로그인 확인
        if ($userData['check']){
            // 권한확인, 권한별로 목록 보여주기
            switch ($userData['classification']) {
            case 'root':
            case 'teacher':
                $where = array(
                    'g.teacherNumber' => $userData['userId']
                );
                $check = true;
                break;
            case 'student':
                $where = array(
                    'gs.userNumber' => $userData['userId']
                );
                $check = true;
                break;
            default:
                $where = false;
                $check = false;
                break;
            }

            // 올바른 권한을 가진 사람만 접근가능
            if ($check){
                $groupDatas = DB::table('groups as g')
                    ->select(
                        'g.number as groupId',
                        'g.name as groupName',
                        DB::raw('count(CASE WHEN ru.retestState = "order" THEN 1 END) as retestStateCount'),
                        DB::raw('count(CASE WHEN ru.wrongState = "order" THEN 1 END) as wrongStateCount')
                    )
                    ->where($where)
                    ->leftJoin('races as r', 'r.groupNumber', '=', 'g.number')
                    ->leftJoin('groupStudents as gs', 'gs.groupNumber', '=', 'g.number')
                    ->leftJoin('raceUsers as ru', function ($join){
                        $join->on('ru.raceNumber', '=', 'r.number');
                        $join->on('ru.userNumber', '=', 'gs.userNumber');
                    })
                    ->groupBy('g.number')
                    ->orderBy('g.number', 'DESC')
                    ->get();

                // 반납할 값 정리
                $groups = array();
                foreach ($groupDatas as $groupData){
                    array_push($groups, array(
                        'groupId' => $groupData->groupId,
                        'groupName' => $groupData->groupName,
                        'retestStateCount' => $groupData->retestStateCount,
                        'wrongStateCount' => $groupData->wrongStateCount
                    ));
                }
                $returnValue = array(
                    'groups'    => $groups,
                    'check'     => true
                );
            } else {
                $returnValue = array(
                    'check'     => false
                );
            }
        } else {
            $returnValue = array(
                'check'     => false
            );
        }

        return $returnValue;
    }

    /****
     * 모바일용 그룹 목록 가져오기
     *
     * @param Request $request->input()
     *      'sessionId' 세션 아이디
     *
     * @return $this->groupsGet($request);
     */
    public function mobileGroupsGet(Request $request){
        return $this->groupsGet($request);
    }

    /****
     * 학생 그룹 목록 가져오기
     *
     * @param Request $request->input()
     *      ['sessionId'] 모바일용 변수
     *
     * @return array(
     *      'groups' => array(
     *          0 => array(
     *              'groupId' 그룹 아이디
     *              'groupName' 그룹 이름
     *              'retestStateCount' 재시험 미응시 카운트
     *              'wrongStateCount' 재시험 미응시 카운트
     *          )
     *      ),
     *      'check' 정보 조회 여부
     *  )
     */
    public function studentGroupsGet(Request $request){
        $postData = array(
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        if ($userData['check'] && ($userData['classification'] == 'student')){
            $groupDatas = DB::table('groupStudents as gs')
                ->select(
                    'g.number as groupId',
                    'g.name as groupName',
                    DB::raw('count(CASE WHEN ru.retestState = "order" THEN 1 END) as retestStateCount'),
                    DB::raw('count(CASE WHEN ru.wrongState = "order" THEN 1 END) as wrongStateCount')
                )
                ->where([
                    'gs.userNumber' => $userData['userId']
                ])
                ->join('groups as g', 'g.number', '=', 'gs.groupNumber')
                ->leftJoin('races as r', 'r.groupNumber', '=', 'g.number')
                ->leftJoin('raceUsers as ru', function ($join){
                    $join->on('ru.raceNumber', '=', 'r.number');
                    $join->on('ru.userNumber', '=', 'gs.userNumber');
                })
                ->groupBy('g.number')
                ->orderBy('g.number', 'DESC')
                ->get();

            $groups = array();
            foreach ($groupDatas as $groupData){
                array_push($groups, array(
                    'groupId' => $groupData->groupId,
                    'groupName' => $groupData->groupName,
                    'retestStateCount' => $groupData->retestStateCount,
                    'wrongStateCount' => $groupData->wrongStateCount
                ));
            }

            $returnValue = array(
                'groups' => $groups,
                'check' => true
            );
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 모바일용 학생 그룹 목록 가져오기
     *
     * @param Request $request->input()
     *      'sessionId' 세션 아이디
     *
     * @return $this->studentGroupsGet(Request $request)
     */
    public function mobileStudentGroupsGet(Request $request){
        return $this->studentGroupsGet($request);
    }

    /****
     * 그룹 정보 가져오기 (그룹 정보, 담당 교사 정보, 학생 정보)
     *
     * @param Request $request->input()
     *      'groupId' 그룹 아이디
     *
     * @return array(
     *      'group' => array(
     *          'id'            그룹 아이디
     *          'name'          그룹 이름
     *          'studentCount'  그룹에 속한 학생 수
     *      ),
     *      'teacher' => array(
     *          'id'    담당 교사 아이디
     *          'name'  담당 교사 이름
     *      ),
     *      'students' => array(
     *          0 => array(
     *              'id'    학생 아이디
     *              'name'  학생 이름
     *          )
     *      ),
     *      'check' 조회 성공 여부
     *  )
     */
    public function groupDataGet(Request $request){
        $postData = array(
            'groupId' => $request->input('groupId')
        );

        // 유저정보 가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 권한 확인
        $where = array();
        switch ($userData['classification']) {
            // 검색방식 설정
            // 1. 자기 그룹 조회
            case 'teacher':
                $where = array(
                    'g.teacherNumber' => $userData['userId']
                );
            // 2. 루트는 모든 그룹 조회 가능
            case 'root':
                // 그룹과 선생정보 가져오기
                $groupData = DB::table('groups as g')
                    ->select(
                        'g.number   as groupId',
                        'g.name     as groupName',
                        'u.number   as teacherId',
                        'u.name     as teacherName'
                    )
                    ->where([
                        'g.number'          => $postData['groupId']
                    ])
                    ->where($where)
                    ->join('users as u', 'u.number', '=', 'g.teacherNumber')
                    ->first();

                // 학생들 가져오기
                $studentData = DB::table('groupStudents as gs')
                    ->select(
                        'gs.userNumber  as userId',
                        'u.name         as userName'
                    )
                    ->where([
                        'gs.groupNumber' => $postData['groupId']
                    ])
                    ->join('users as u', 'u.number', '=', 'gs.userNumber')
                    ->get();

                $students = array();
                foreach ($studentData as $student){
                    array_push($students, array(
                        'id'    => $student->userId,
                        'name'  => $student->userName
                    ));
                }

                // 반납하는 값
                $returnValue = array(
                    'group' => array(
                        'id'            => $groupData->groupId,
                        'name'          => $groupData->groupName,
                        'studentCount'  => count($students)
                    ),
                    'teacher' => array(
                        'id'    => $groupData->teacherId,
                        'name'  => $groupData->teacherName
                    ),
                    'students' => $students,
                    'check' => true
                );
                break;

            // 2. 권한 외
            default:
                // 반납하는 값
                $returnValue = array(
                    'check' => false
                );
                break;
        }

        return $returnValue;
    }

    /****
     * 그룹 만들기
     *
     * @param Request $request->input()
     *      'groupName' 그룹 이름
     *
     * @return array(
     *      'group' => array(
     *          'id' 그룹 아이디
     *          'name' 그룹 이름
     *          'studentCount' 호완용 변수 - 그룹 학생 수(지금 만들어서 0명)
     *      ),
     *      'teacher' => array(
     *          'id' 담당 교사 아이디
     *          'name' 담당 교사 이름
     *      ),
     *      'students' 호완용 변수 - 그룹 학생 수(지금 만들어서 0명)
     *      'check' 검색 성공 여부
     *  )
     */
    public function createGroup(Request $request){
        $postData = array(
            'groupName' => $request->input('groupName')
        );

        // 유저확인
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 유저 로그인 확인
        if ($userData['check']) {
            // 권한확인
            switch ($userData['classification']) {
                case 'root':
                case 'teacher':
                    $groupId = DB::table('groups')
                        ->insertGetId([
                            'name'          => $postData['groupName'],
                            'teacherNumber' => $userData['userId']
                        ]);
                    $check = true;
                    break;
                default:
                    $groupId = false;
                    $check = false;
                    break;
            }

            // 반납는 값
            if ($check && $groupId){
                $returnValue = array(
                    'group' => array(
                        'id'            => $groupId,
                        'name'          => $postData['groupName'],
                        'studentCount'  => 0 // 갓 만들었기 때문에 없음.
                    ),
                    'teacher' => array(
                        'id'    => $userData['userId'],
                        'name'  => $userData['userName']
                    ),
                    'students'  => array(), // 호환용 비어있는 변수
                    'check'     => true
                );
            } else {
                $returnValue = array(
                    'check' => false
                );
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 그룹에 학생 등록하기
     *
     * @param Request $request->input()
     *      'groupId' 그룹 아이디
     *      // 해당 그룹에 등혹할 학생 목록
     *      'students' => array(
     *          0 => array(
     *              'id' 학생 아이디
     *              'name' 학생 이름
     *          )
     *      )
     *
     * @return array(
     *      // 등록에 성공한 학생 목록
     *      'students' => array(
     *          0 => array(
     *              'id' 학생 아이디
     *              'name' 학생 이름
     *          )
     *      ),
     *      'check' 등록 성공 여부
     *  )
     */
    public function pushInvitation(Request $request){
        $postData = array(
            'groupId' => $request->input('groupId'),
            'students' => json_decode($request->input('students'))
        );

        // 유저 정보가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 유저권한확인
        if ($userData['check']) {
            $where = array();
            // 유저 추가
            switch ($userData['classification']) {
                case 'teacher':
                    $where = array('teacherNumber' => $userData['userId']);
                case 'root':
                    // 그룹에 대한 권한 확인
                    $groupData = DB::table('groups')
                        ->select(
                            'number         as groupId',
                            'teacherNumber  as teacherId'
                        )
                        ->where([
                            'number' => $postData['groupId']
                        ])
                        ->where($where)
                        ->first();

                    // 권한 확인
                    if ($groupData) {
                        $inputStudentIds = array();
                        foreach ($postData['students'] as $student){
                            array_push($inputStudentIds, $student->id);
                        }

                        // 그룹에 이미 가입된 유저들 검색
                        $groupUsers = DB::table('users as u')
                            ->where(function ($query){
                                $query->where('u.classification', '=', 'student')
                                    ->orWhere('u.classification', '=', 'sleepStudent');
                            })
                            ->where('gs.groupNumber', '=', $postData['groupId'])
                            ->whereIn('u.number', $inputStudentIds)
                            ->leftJoin('groupStudents as gs', 'gs.userNumber', '=', 'u.number')
                            ->pluck('u.number')
                            ->toArray();

                        // 그룹에 가입안된 학생 검색
                        $noGroupStudents = array_diff($inputStudentIds, $groupUsers);
                        $memberStudents = DB::table('users')
                            ->whereIn('number', $noGroupStudents)
                            ->where(function ($query){
                                $query->where('classification', '=', 'student')
                                    ->orWhere('classification', '=', 'sleepStudent');
                            })
                            ->pluck('number')
                            ->toArray();

                        // 회원가입 안된 학생 처리하기
                        $noMemberStudents = array_diff($noGroupStudents, $memberStudents);
                        $i = 0;
                        foreach ($noMemberStudents as $studentId){
                            for (; $i < count($postData['students']) ; $i++){
                                if ($postData['students'][$i]->id == $studentId){
                                    DB::table('users')
                                        ->insert([
                                            'number'            => $studentId,
                                            'name'              => $postData['students'][$i]->name,
                                            'pw'                => $studentId,
                                            'classification'    => 'student'
                                        ]);
                                    break;
                                }
                            }
                        }

                        // 등록할 학생들 처리
                        $studentIds = array();
                        foreach ($noGroupStudents as $studentId) {
                            array_push($studentIds, array(
                                'groupNumber' => $groupData->groupId,
                                'userNumber' => $studentId
                            ));
                        }

                        // 학생 등록하기
                        DB::table('groupStudents')
                            ->insert($studentIds);

                        // 방금 등록된 학생 검색
                        $newInStudents = DB::table('users as u')
                            ->select(
                                'u.number   as userId',
                                'u.name     as userName'
                            )
                            ->where(function ($query){
                                $query->where('classification', '=', 'student')
                                    ->orWhere('classification', '=', 'sleepStudent');
                            })
                            ->where('gs.groupNumber', '=', $postData['groupId'])
                            ->whereIn('u.number', $noGroupStudents)
                            ->leftJoin('groupStudents as gs', 'gs.userNumber', '=', 'u.number')
                            ->get();

                        // 반납하는 값
                        $students = array();
                        foreach ($newInStudents as $student) {
                            array($students, array(
                                'id' => $student->userId,
                                'name' => $student->userName
                            ));
                        }
                        $returnValue = array(
                            'students' => $studentIds,
                            'check' => true
                        );
                    } else {
                        $returnValue = array(
                            'check' => false
                        );
                    }
                    break;
                default:
                    $returnValue = array(
                        'check' => false
                    );
                    break;
            }
        }
        else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 해당 그룹에 포함되지 않은 유저 검색
     *
     * @param Request $request->input()
     *      'search' 검색어(학번, 이름)
     *      'groupId' 그룹 아이디
     *
     * @return array(
     *      'users' => array(
     *          0 => array(
     *              'id' 학생 아이디
     *              'name' 학생 이름
     *              'classification' 유저의 권한(학생, 교사 등)
     *          )
     *      ),
     *      'check' 검색 성공 여보
     *  )
     */
    public function selectUser(Request $request){
        $postData = array(
            'search'    => $request->input('search'),
            'groupId'   => $request->input('groupId')
        );

        // 세션정보 가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 로그인 확인
        if ($userData['check']) {

            // 권한 확인
            switch ($userData['classification']) {
                // 검색방식 설정
                // 1. 미등록 학생
                // 해당 그룹에 미등록된 학생만 검색
                // 이름, 학번에 해당 문자가 포감되는지 확인
                // 학생만 검색
                case 'teacher':
                case 'root':
                    // 그룹에 포함된 학생 검색
                    $groupUsers = DB::table('users as u')
                        ->where(function ($query){
                            $query->where('classification', '=', 'student')
                                ->orWhere('classification', '=', 'sleepStudent');
                        })
                        ->where(function ($query) use ($postData) {
                            $query->where('u.number', 'like', '%' . $postData['search'] . '%')
                                ->orWhere('u.name', 'like', '%' . $postData['search'] . '%');
                        })
                        ->where('gs.groupNumber', '=', $postData['groupId'])
                        ->leftJoin('groupStudents as gs', 'gs.userNumber', '=', 'u.number')
                        ->pluck('u.number')
                        ->toArray();

                    // 그룹에 포함안된 학생 검색
                    $users = DB::table('users')
                        ->select(
                            'number           as id',
                            'name             as name',
                            'classification   as classification'
                        )
                        ->where(function ($query){
                            $query->where('classification', '=', 'student')
                                ->orWhere('classification', '=', 'sleepStudent');
                        })
                        ->whereNotIn('number', $groupUsers)
                        ->where(function ($query) use ($postData) {
                            $query->where('number', 'like', '%' . $postData['search'] . '%')
                                ->orWhere('name', 'like', '%' . $postData['search'] . '%');
                        })
                        ->orderBy('number', 'desc')
                        ->get();
                    break;

                // 2. 루트의 검색
                // 이름, 학번에 해당 문자가 포감되는지 확인
                // 교사등 모든학생 검색
                // 미구현
//            case 'root':
//                break;

                // 3. 권한 외
                default:
                    $users = false;
                    break;
            }

            // 반납하는 값
            if ($users) {
                $userArr = array();
                foreach ($users as $user) {
                    array_push($userArr, array(
                        'id' => $user->id,
                        'name' => $user->name,
                        'classification' => $user->classification
                    ));
                }

                $returnValue = array(
                    'users' => $userArr,
                    'check' => true
                );
            } else {
                $returnValue = array(
                    'check' => false
                );
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 비밀번호 잊은 학생을 위해 비밀번호 변경
     *
     * @param Request $request->input()
     *      'userId' 학생 아이디
     *      'password' 변경할 비밀번호
     *
     * @return array(
     *      'check'변경 성공 여부
     *  )
     */
    public function studentModify(Request $request){
        $postData = array(
            'userId'        => $request->input('userId'),
            'password'      => $request->input('password')
        );

        // 유저정보 가져오기기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 권한확인
        if ($userData['check']){
            switch ($userData['classification']){
                case 'root':
                case 'teacher':
                    // 유저 페스워드 변경하기
                    $updateState = DB::table('users')
                        ->where('number', '=', $postData['userId'])
                        ->update([
                            'pw' => $postData['password']
                        ]);

                    // 업데이트 성공 실패 확인
                    $updateState = ($updateState == 1);

                    // 반납할 값 정리 2
                    $returnValue = array(
                        'check' => $updateState
                    );
                    break;
                default:
                    $returnValue = array(
                        'check' => false
                    );
                    break;
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 학생 그룹에서 제외
     *
     * @param Request $request->input()
     *      'userId' 학생 아이디
     *      'groupId' 그룹 아이디
     *
     * @return array(
     *      'check' 그룹에서 제외 성공
     *  )
     */
    public function studentGroupExchange(Request $request){
        // 요구하는 값
        $postData = array(
            'userId'        => $request->input('userId'),
            'groupId'       => $request->input('groupId')
        );

        // 유저정보 가져오기기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 권한확인
        if ($userData['check']) {
            $where = array();
            switch ($userData['classification']) {
                case 'teacher':
                    $where = array('g.teacherNumber' => $userData['userId']);
                case 'root':
                    // 학생 제외
                    $deleteState = DB::table('groupStudents as gu')
                        ->where('gu.userNumber', '=', $postData['userId'])
                        ->where($where)
                        ->join('groups as g', 'g.number', '=', 'gu.groupNumber')
                        ->delete();

                    $deleteState = ($deleteState == 1);

                    $returnValue = array(
                        'check' => $deleteState
                    );
                    break;
                default:
                    $returnValue = array(
                        'check' => false
                    );
                    break;
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /* 미구현
    // 교사 임명 root
    // 미구현
    public function teacherEmpowerment(Request $request){
        // 요구하는 값
        $postData = array(
            'userId',
            'classification'
        );

        // 반납하는 값
        $returnValue = array(
            'userId',
            'classification',
            'check'
        );

        return $returnValue;
    }

    // 그룹 담당 교사 변경 root
    // 미구현
    public function teacherGroupExchange(Request $request){
        // 요구하는 값
        $postData = array(
            'groupId',
            'userId'
        );

        // 반납하는 값
        $returnValue = array(
            'groupId',
            'userIdBefore',
            'userIdAfter',
            'check'
        );

        return $returnValue;
    }
    */
}

?>