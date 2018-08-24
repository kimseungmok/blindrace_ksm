<?php
namespace app\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use App\Http\Controllers\UserController;

class RaceController extends Controller{

    /****
     * List選択後QuizRaceとかPopQuizの準備をする。
     *
     * @param Request $request->input()
     *      'groupId' 出題するクラスのアイディ
     *      'raceType' レースのタイプ
     *      'listId' 出題するリストのアイディ
     *      'passingMark' 再試験のためのカットライン
     *
     * @return view('homepage')->with('response', $returnValue)
     *      $returnValue => array(
     *          'list'=>array(
     *              'listName' リストの名前
     *              'quizCount' リストの問題数
     *          ),
     *          'group'=>array(
     *              'groupName' グループの名前
     *              'groupStudentCount' グループの生徒数
     *          ),
     *          'sessionId' 教師のsessionのアイディ
     *          'check' レースの生成に成功するかどうか
     *          'roomPin' 部屋のアイディ
     *          'quizs' => $this->quizGet(出題するリストのアイディ);
     *      )
     */
    public function createRace(Request $request){
        $postData = array(
            'groupId'       => $request->input('groupId'),
            'raceType'      => $request->input('raceType'),
            'listId'        => $request->input('listId'),
            'passingMark'   => $request->input('passingMark')
        );

        // 로그인된 유저의 세션 정보 가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 레이스를 시작하려는 그룹이 해당 유저의 그룹이 맞는지 확인
        // 그룹의 정보 가져오기
        $groupData = DB::table('groups as g')
            ->select(
                'g.number                       as groupId',
                'g.name                         as groupName',
                DB::raw('COUNT(gs.userNumber)   as studentCount')
            )
            ->join('groupStudents as gs', 'gs.groupNumber', '=', 'g.number')
            ->where([
                'g.number'          => $postData['groupId'],
                'g.teacherNumber'   => $userData['userId']
            ])
            ->groupBy('g.number')
            ->first();

        // 해당 리스트의 존재확인
        $listData = DB::table('lists as l')
            ->select(
                'l.name                         as listName',
                'l.number                       as listId',
                DB::raw('COUNT(lq.quizNumber)   as quizCount')
            )
            ->join('listQuizs as lq', 'lq.listNumber', '=', 'l.number')
            ->join('folders as f', 'f.number', '=', 'l.folderNumber')
            ->where([
                'l.number' => $postData['listId'],
            ])
            ->where(function ($query) use ($userData){
                $query->where([
                        'f.teacherNumber' => $userData['userId']
                     ])
                    ->orWhere([
                        'l.openState' => self::OPEN_STATE
                    ]);
            })
            ->groupBy('l.number')
            ->first();

        // 레이스와 그룹이 존재하면 시작
        if(!((!$listData) || (!$groupData))) {
            // 재시험 점수 값 확인
            if(($postData['passingMark']) < 0 || ($postData['passingMark'] > 100)){
                $postData['passingMark'] = 60;
            }

            // 레이스 정보를 저장
            $raceId = DB::table('races')->insertGetId([
                'groupNumber'   => $groupData->groupId,
                'teacherNumber' => $userData['userId'],
                'listNumber'    => $listData->listId,
                'passingMark'   => $postData['passingMark'],
                'type'          => $postData['raceType']
            ], 'number');

            // 중복 없는 방 번호 입력
            do{
                // 랜덤 값 지정
                $roomPin = rand(100000, 999999);

                // 교사 세션에 데이터 저장
                DB::table('sessionDatas')
                    ->where('number', '=', $request->session()->get('sessionId'))
                    ->update([
                        'raceNumber'        => $raceId,
                        'PIN'               => $roomPin,
                        'nick'              => '',
                        'characterNumber'   => null
                    ]);

                // 같은 방번호를 가진 사람이 있는가?
                $roomCheck = DB::table('sessionDatas')
                    ->select('PIN')
                    ->where(['PIN' => $roomPin])
                    ->where('number', '<>', $request->session()->get('sessionId'))
                    ->first();
            }while($roomCheck);

            // 반납할 값 정리
            $returnValue = array(
                'list'=>array(
                    'listName'  => $listData->listName,
                    'quizCount'  => $listData->quizCount
                ),
                'group'=>array(
                    'groupName'         => $groupData->groupName,
                    'groupStudentCount' => $groupData->studentCount
                ),
                'sessionId'     => $request->session()->get('sessionId'),
                'check'         => true,
                'roomPin'       => $roomPin,
                'quizs'         => $this->quizGet($listData->listId)
            );
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        // 값을 반납
        // return $returnValue;
        $view = 'homepage';
        if ($returnValue['check']){
            if ($postData['raceType'] == 'race') {
                $view = 'Race/race_waiting';
            } else if ($postData['raceType'] == 'popQuiz') {
                $view = 'Race/race_popquiz';
            }
        }
        return view($view)->with('response', $returnValue);
    }

    /****
     * レースとテストで、生徒が部屋に入場する時
     *
     * @param Request $request
     *      'roomPin' 部屋のアイディ
     *      'sessionId' 生徒のsessionのアイディ
     *
     * @return array(
     *      'sessionId' 生徒のsessionのアイディ
     *      'characters' すでに選択されたキャラクターのアイディの一覧
     *      ['quizs'] 小テスト再接続の際、解けない問題リスト
     *      'check' 部屋に入場成功するかどうか
     *  )
     */
    public function studentIn(Request $request){
        $postData = array(
            'roomPin'       => $request->input('roomPin'),
            'sessionId'     => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 로그인 확인
        $userData = UserController::sessionDataGet($postData['sessionId']);
        
        // 재입장시
        if ($userData['check'] && ($userData['roomPin'] == $postData['roomPin'])) {
            $raceData = DB::table('races')
                ->select(
                    'listNumber as listId',
                    'type'
                )
                ->where([
                    'number' => $userData['raceId']
                ])
                ->first();

            if ($raceData->type == 'popQuiz'){

                $playData = DB::table('listQuizs as lq')
                    ->where([
                        're.raceNo' => $userData['raceId'],
                        're.userNo' => $userData['userId'],
                        're.retest' => self::RETEST_NOT_STATE
                    ])
                    ->leftJoin('records as re', function ($join) {
                        $join->on('re.quizNo', '=', 'lq.quizNumber');
                        $join->on('re.listNo', '=', 'lq.listNumber');
                    })
                    ->pluck('lq.quizNumber')
                    ->toArray();

                $quizData = DB::table('quizBanks as qb')
                    ->select(
                        'qb.number          as number',
                        'qb.question        as question',
                        'qb.hint            as hint',
                        'qb.rightAnswer     as rightAnswer',
                        'qb.example1        as example1',
                        'qb.example2        as example2',
                        'qb.example3        as example3',
                        'qb.type            as type'
                    )
                    ->where([
                        'lq.listNumber' => $raceData->listId
                    ])
                    ->whereNotIn('lq.quizNumber', $playData)
                    ->join('listQuizs as lq', 'lq.quizNumber', '=', 'qb.number')
                    ->get();

                // 반납값 정리
                $quizs = array();
                foreach ($quizData as $quiz) {
                    $type = explode(' ', $quiz->type);
                    array_push($quizs, array(
                        'quizId' => $quiz->number,
                        'question' => $quiz->question,
                        'hint' => $quiz->hint,
                        'right' => $quiz->rightAnswer,
                        'example1' => $quiz->example1,
                        'example2' => $quiz->example2,
                        'example3' => $quiz->example3,
                        'quizType' => $type[0],
                        'makeType' => $type[1]
                    ));
                }

                $returnValue = array(
                    'userName' => $userData['userName'],
                    'sessionId' => $postData['sessionId'],
                    'characters' => array(),
                    'quizs' => $quizs,
                    'check' => true
                );
            } else if ($raceData->type == 'race'){
                $returnValue = array(
                    'sessionId' => $postData['sessionId'],
                    'check' => false,
                    'mark' => 3
                );
            } else {
                $returnValue = array(
                    'sessionId' => $postData['sessionId'],
                    'check' => false,
                    'mark' => 4
                );
            }
        } else if($userData['check']){
            // 해당 학생이 참가한 레이스의 정보 및 해당 그룹 학생인지 확인
            $raceData = DB::table('races as r')
                ->select([
                    'r.number as raceId',
                    'r.type as raceType'
                ])
                ->where([
                    'gs.userNumber' => $userData['userId'],
                    's2.PIN' => $postData['roomPin'],
                    's2.nick' => ''
                ])
                ->join('groupStudents as gs', 'gs.groupNumber', '=', 'r.groupNumber')
                ->join('sessionDatas as s2', 's2.raceNumber', '=', 'r.number')
                ->first();

            if ($raceData) {
                // 유저 세션 갱신
                $sessionUpdate = DB::table('sessionDatas')
                    ->where([
                        'number' => $postData['sessionId']
                    ])
                    ->update([
                        'PIN' => $postData['roomPin'],
                        'raceNumber' => $raceData->raceId,
                        'characterNumber' => null,
                        'nick' => ($raceData->raceType == 'race' ? null : $userData['userName']),
                    ]);
                $sessionCheck = ($sessionUpdate == 1);

                // 갱신 성공시 유저정보 입력
                $characters = array();
                if ($sessionCheck) {
                    DB::table('raceUsers')
                        ->insert([
                            'raceNumber' => $raceData->raceId,
                            'userNumber' => $userData['userId'],
                            'retestState' => 'not',
                            'wrongState' => 'not'
                        ]);

                    $characters = DB::table('sessionDatas')
                        ->where([
                            'PIN' => $postData['roomPin']
                        ])
                        ->whereNotNull('characterNumber')
                        ->pluck('characterNumber')
                        ->toArray();
                }

                // 반납값 정리
                $returnValue = array(
                    'userName' => $userData['userName'],
                    'sessionId' => $postData['sessionId'],
                    'characters' => $characters,
                    'check' => $sessionCheck
                );
            } else {
                $returnValue = array(
                    'sessionId' => $postData['sessionId'],
                    'check' => false,
                    'mark' => 2
                );
            }
        } else {
            $returnValue = array(
                'sessionId' => $postData['sessionId'],
                'check' => false,
                'mark' => 1
            );
        }

        return $returnValue;
    }

    /****
     * レースが始まる前に生徒が出た場合
     *
     * @param Request $request->input()
     *      'roomPin' 部屋のアイディ
     *      'sessionId' 生徒のsessionのアイディ
     *
     * @return array(
     *      'sessionId' 生徒のsessionのアイディ
     *      'characters' すでに選択されたキャラクターのアイディの一覧
     *      'check' 部屋に入場成功するかどうか
     *  )
     */
    public function studentOut(Request $request){
        $postData = array(
            'roomPin'       => $request->input('roomPin'),
            'sessionId'     => $request->input('sessionId')
        );

        // 해당유저 확인
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 해당 학생이 참가한 레이스의 정보 및 해당 그룹 학생인지 확인
        $raceData = DB::table('races as r')
            ->select([
                'r.number as raceId',
                'r.type as raceType'
            ])
            ->where([
                'gs.userNumber'         => $userData['userId'],
                's2.PIN'                => $postData['roomPin'],
                's2.nick'               => ''
            ])
            ->join('groupStudents as gs', 'gs.groupNumber', '=', 'r.groupNumber')
            ->join('sessionDatas as s2', 's2.raceNumber', '=', 'r.number')
            ->first();

        if ($raceData) {
            // 유저 세션 갱신
            $character = DB::table('sessionDatas')
                ->select('characterNumber')
                ->where([
                    'PIN' => $postData['roomPin'],
                    'number' => $postData['sessionId']
                ])
                ->whereNotNull('characterNumber')
                ->first();

            DB::table('sessionDatas')
                ->where([
                    'number' => $postData['sessionId']
                ])
                ->update([
                    'PIN'               => null,
                    'raceNumber'        => null,
                    'characterNumber'   => null,
                    'nick'              => null,
                ]);

            DB::table('raceUsers')
                ->where([
                    'raceNumber'    => $raceData->raceId,
                    'userNumber'    => $userData['userId']
                ])
                ->delete();

            // 반납값 정리
            $returnValue = array(
                'sessionId'     => $postData['sessionId'],
                'characters'    => $character ? $character->characterNumber : false,
                'check'         => true
            );
        } else {
            $returnValue = array(
                'sessionId'     => $postData['sessionId'],
                'check'         => false
            );
        }

        return $returnValue;
    }

    /****
     * レースで、生徒がニックネームとキャラクターを設定するとき
     *
     * @param Request $request->input()
     *      'sessionId' 세션 아이디
     *      'nick' 닉네임
     *      'characterId' 캐릭터 번호
     *
     * @return array(
     *      'nickCheck' 닉네임 중복 확인 - 성공 여부
     *      'characterCheck' 캐릭터 종복 확인 - 성공 여부
     *      'characterId' 선택한 캐릭터 아이디
     *      'check' 쿼리 성공 여부
     *  )
     */
    public function studentSet(Request $request){
        $postData = array(
            'sessionId'     => $request->input('sessionId'),
            'nick'          => $request->input('nick'),
            'characterId'   => $request->input('characterId')
        );

        // 해당 학생이 레이스에 참가중인지 확인
        $Data = DB::table('sessionDatas as s1')
            ->select(
                's2.raceNumber as raceId'
            )
            ->where([
                's1.number'         => $postData['sessionId'],
                's2.nick'           => '',
            ])
            ->where(function ($query){
                $query->where('s1.nick', '<>', '')
                    ->orWhereNull('s1.nick');
            })
            ->join('sessionDatas as s2', function ($join){
                $join->on('s2.PIN', '=', 's1.PIN');
                $join->on('s2.raceNumber', '=', 's1.raceNumber');
            })
            ->first();

        if ($Data) {
            // 닉네임 중복확인
            $nickUpdate = DB::table('sessionDatas')
                ->where([
                    'number' => $postData['sessionId']
                ])
                ->update([
                    'nick'  => $postData['nick']
                ]);
            $nickCheck = ($nickUpdate == 1);

            // 캐릭터 중복확인
            $characterData = DB::table('sessionDatas')
                ->where([
                    'number' => $postData['sessionId']
                ])
                ->update([
                    'characterNumber'   => $postData['characterId']
                ]);
            $characterCheck = ($characterData == 1);

            // 반납값 정리
            $returnValue = array(
                'nickCheck'         => $nickCheck,
                'characterCheck'    => $characterCheck,
                'characterId'       => $postData['characterId'],
                'check'             => true
            );
        } else {
            $returnValue = array(
                'check'             => false
            );
        }

        return $returnValue;
    }

    /****
     * 레이스 혹은 테스트에서 학생들의 정답들을 DB에 입력
     *
     * @param Request $request->input()
     *      'sessionId' 세션 아이디
     *      'roomPin' 방 아이디
     *      'quizId' 퀴즈 아이디
     *      'answer' 학생이 입력한 정답
     *
     * @return array(
     *      'check' 문제 입력 성공 여부
     *  )
     */
    public function answerIn(Request $request){
        $postData     = array(
            'sessionId' => $request->input('sessionId'),
            'roomPin'   => $request->input('roomPin'),
            'quizId'    => $request->input('quizId'),
            'answer'    => $request->input('answer')
        );

        // 유저 정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 정답 미입력시 오답처리
        if (!$postData['answer']){
            $postData['answer'] = '';
        }

        // 레이스 정보 가져오기
        $raceData = DB::table('races as r')
            ->select(
                'r.number as raceId',
                'r.listNumber as listId'
            )
            ->where([
                's.PIN'     => $postData['roomPin'],
                's.nick'    => ''
            ])
            ->join('sessionDatas as s', 's.raceNumber', '=', 'r.number')
            ->first();

        // 레이스가 존재할 경우 값을 입력
        if($raceData){
            // 현재 문제정보
            $quizData = DB::table('quizBanks')
                ->select(
                    'rightAnswer as right',
                    'type'
                )
                ->where([
                    'number' => $postData['quizId']
                ])
                ->first();

            switch ($quizData->type){
                case 'vocabulary obj':
                case 'word obj':
                case 'grammar obj':
                case 'vocabulary sub':
                case 'word sub':
                case 'grammar sub':
                    $rights = explode(',', $quizData->right);
                    $answerCheck = 'X';
                    foreach ($rights as $right){
                        if ($postData['answer'] == $right){
                            $answerCheck = 'O';
                            break;
                        }
                    }
                    break;
                default:
                    $answerCheck = 'X';
            }

            // 정답을 입력
            $quizInsert = DB::table('records')
                ->insert([
                    'raceNo'        => $raceData->raceId,
                    'userNo'        => $userData['userId'],
                    'listNo'        => $raceData->listId,
                    'quizNo'        => $postData['quizId'],
                    'answerCheck'   => $answerCheck,
                    'answer'        => $postData['answer']
                ]);

            // true 값 입력 성공
            // false 재 시간 이내에 정답 입력실패, 중복입력, 레이스가 없음, 리스트가 없음.
            if ($quizInsert == 1){
                $returnValue = array(
                    'check' => true
                );
            } else{
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
     * 레이스에서 중간 및 최종 결과용
     *
     * @param Request $request->input()
     *      'sessionId' 세션 아이디
     *      'quizId' 퀴즈 아이디
     *
     * @return array(
     *      'studentResults'    => array(
     *          0 => array(
     *              'sessionId' 새션 아이디
     *              'nick' 닉네임
     *              'characterId' 선택한 캐릭터 번호
     *              'rightCount' 현재까지 정답 갯수
     *              'answer' 지금 입력한 정답
     *              'answerCheck' 지금 문제 정답 여부
     *          )
     *      ),
     *      'rightAnswer' 정답자 수
     *      'wrongAnswer' 오답자 수
     *      'check' 조회 성공 여부
     *  )
     */
    public function result(Request $request){
        $postData = array(
            'sessionId' => $request->input('sessionId'),
            'quizId'    => $request->input('quizId')
        );

        // 세션정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 레이스 정보 가져오기
        $raceData = DB::table('races')
            ->select(
                'number as raceId',
                'listNumber as listId'
            )
            ->where([
                'number' => $userData['raceId']
            ])
            ->first();

        if ($raceData){
            // 참가 학생목록
            $students = DB::table('raceUsers as ru')
                ->select(
                    'ru.userNumber      as userId',
                    's.number           as sessionId',
                    's.nick             as nick',
                    's.characterNumber  as characterId',
                    DB::raw('COUNT(CASE WHEN re.quizNo = "'.$postData['quizId'].'" THEN 1 END) as lastQuizId'),
                    DB::raw('COUNT(CASE WHEN re.answerCheck="O" THEN 1 END) as rightCount')
                )
                ->where([
                    'ru.raceNumber' => $raceData->raceId,
                    're.retest' => self::RETEST_NOT_STATE
                ])
                ->whereNotNull('s.nick')
                ->leftJoin('records as re', function ($join){
                    $join->on('re.raceNo', '=', 'ru.raceNumber');
                    $join->on('re.userNo', '=', 'ru.userNumber');
                })
                ->join('sessionDatas as s', 's.userNumber', '=', 'ru.userNumber')
                ->orderBy('rightCount', 'userId')
                ->groupBy('userId')
                ->get();

            // 값 정리 시작
            $studentResults = array();
            $rightAnswer = 0;
            $wrongAnswer = 0;
            foreach($students as $student) {
                // 미입력자 처리
                if ($student->lastQuizId == 0) {
                    DB::table('records')
                        ->insert([
                            'raceNo' => $raceData->raceId,
                            'userNo' => $student->userId,
                            'listNo' => $raceData->listId,
                            'quizNo' => $postData['quizId'],
                            'answer' => '',
                            'answerCheck' => 'X'
                        ]);

                    array_push($studentResults, array(
                        'sessionId' => $student->sessionId,
                        'nick' => $student->nick,
                        'characterId' => $student->characterId,
                        'rightCount' => $student->rightCount,
                        'answer' => '',
                        'answerCheck' => 'X'
                    ));
                    $wrongAnswer++;
                } else {
                    // 입력한 사람 정답여부 처리하기
                    $studentAnswer = DB::table('records')
                        ->select(
                            'answerCheck',
                            'answer'
                        )
                        ->where([
                            'raceNo' => $raceData->raceId,
                            'userNo' => $student->userId,
                            'listNo' => $raceData->listId,
                            'quizNo' => $postData['quizId']
                        ])
                        ->first();

                    if ($studentAnswer->answerCheck == 'O') {
                        $rightAnswer++;
                    } else {
                        $wrongAnswer++;
                    }
                    array_push($studentResults, array(
                        'sessionId' => $student->sessionId,
                        'nick' => $student->nick,
                        'characterId' => $student->characterId,
                        'rightCount' => $student->rightCount,
                        'answer' => $studentAnswer->answer,
                        'answerCheck' => $studentAnswer->answerCheck
                    ));
                }
            }

            // 반납값 정리
            $returnValue = array(
                'studentResults'    => $studentResults,
                'rightAnswer'       => $rightAnswer,
                'wrongAnswer'       => $wrongAnswer,
                'check'             => true
            );
        }
        else{
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 레이스 혹은 테스트에서 종료 후 세션 정리
     *
     * @param Request $request
     *     로그인 되어있기만 하면되고 요구하는 값은 없음
     *
     * @return array(
     *      'students' => array(
     *          0 => array(
     *              'sessionId' 세션 아이디
     *              'nick' 닉네임
     *              'characterId' 케릭터 번호
     *              'rightCount' 정답 갯수
     *              'retestState' 재시험 여부
     *              'wrongState' 오답노트 여부
     *          )
     *      ),
     *      'check' 결과 조회 성공 여부
     *  )
     */
    public function raceEnd(Request $request){
        // 선생정보 가져오기기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        if ($userData['roomPin']) {
            // 시험정보 가져오기
            $raceData = DB::table('races as r')
                ->select(
                    'l.name as listName',
                    'r.passingMark as passingMark',
                    'l.number as listId',
                    'r.type as type',
                    DB::raw('count(lq.quizNumber) as quizCount')
                )
                ->where([
                    'r.number' => $userData['raceId']
                ])
                ->join('lists as l', 'l.number', '=', 'r.listNumber')
                ->join('listQuizs as lq', 'lq.listNumber', '=', 'l.number')
                ->groupBy('r.number')
                ->first();

            // 최종 성적 정보 가져오기
            $students = DB::table('records as re')
                ->select(
                    's.number           as sessionId',
                    's.nick             as nick',
                    's.characterNumber  as characterId',
                    's.userNumber       as userId',
                    DB::raw('COUNT(CASE WHEN re.answerCheck = "O" THEN 1 END) as rightCount')
                )
                ->where([
                    're.raceNo' => $userData['raceId'],
                    're.retest' => self::RETEST_NOT_STATE
                ])
                ->join('sessionDatas as s', 's.userNumber', '=', 're.userNo')
                ->orderBy('rightCount', 's.userNumber')
                ->groupBy('s.userNumber')
                ->get();

            // 미제출 문제 처리하기
            foreach ($students as $student) {
                $this->omission($student->userId, $userData['raceId'], self::RETEST_NOT_STATE);
            }

            // 재시험 여부 확인하기
            $retestTargets = array();
            $wrongTargets = array();
            if ($raceData) {
                foreach ($students as $student) {
                    if ($raceData->passingMark > (($student->rightCount / $raceData->quizCount) * 100)) {
                        array_push($retestTargets, $student->userId);
                    }
                    if ($student->rightCount < $raceData->quizCount) {
                        array_push($wrongTargets, $student->userId);
                    }
                }
                // 재시험 상태 등록
                DB::table('raceUsers')
                    ->where('raceNumber', '=', $userData['raceId'])
                    ->whereIn('userNumber', $retestTargets)
                    ->update([
                        'retestState' => 'order'
                    ]);
                // 오답노트 상태 등록
                DB::table('raceUsers')
                    ->where('raceNumber', '=', $userData['raceId'])
                    ->whereIn('userNumber', $wrongTargets)
                    ->update([
                        'wrongState' => 'order'
                    ]);

                // 세션 초기화
                DB::table('sessionDatas')
                    ->where([
                        'PIN' => $userData['roomPin']
                    ])
                    ->update([
                        'nick' => null,
                        'PIN' => null,
                        'characterNumber' => null,
                        'raceNumber' => null
                    ]);

                // 반납값 정리
                $studentData = array();
                foreach ($students as $student) {
                    array_push($studentData, array(
                        'sessionId' => $student->sessionId,
                        'nick' => $student->nick,
                        'characterId' => $student->characterId,
                        'quizCount' => $raceData->quizCount,
                        'rightCount' => $student->rightCount,
                        'score' => (($student->rightCount / $raceData->quizCount) * 100),
                        'retestState' => in_array($student->userId, $retestTargets),
                        'wrongState' => in_array($student->userId, $wrongTargets)
                    ));
                }
                $returnValue = array(
                    'students' => $studentData,
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
     * 웹용 재시험 대상 레이스 목록 가져오기
     *
     * @param Request $request->input()
     *     ['sessionId'] 모바일용 변수
     *
     * @return array(
     *      'lists' => $this->selectRetestList(유저 아이디),
     *      'check' 조회 성공 여부
     *  )
     */
    public function getRetestListWeb(Request $request){
        $postData = array(
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저 정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        if ($userData['check']) {
            $returnValue = array(
                'lists' => $this->selectRetestList($userData['userId']),
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
     * 모바일용 재시험 대상 레이스 목록 가져오기
     *
     * @param Request $request->input()
     *      'sessionId' 세션 아이디
     *
     * @return $this->getRetestListWeb(세션 아이디);
     */
    public function getRetestListMobile(Request $request){
        $postData = array(
            'sessionId' => $request->input('sessionId')
        );

        return $this->getRetestListWeb($postData['sessionId']);
    }

    /****
     * 웹 용 재시험 준비
     *
     * @param Request $request->input()
     *      'raceId' 레이스 아이디
     *
     * @return view('Race/race_retest')->with('response', $returnValue);
     *      $returnValue => array(
     *          'sessionId' 세션 아이디
     *          'raceId' 레이스 아이디
     *          ['retestState' => $raceCheck->retestState,] 대상자가 아닐경우
     *          'check' 준비 성공여부
     *      ).
     */
    public function retestSet(Request $request){
        $postData = array(
            'raceId' => $request->has('raceId') ? $request->input('raceId') : false
        );

        // 유저정보 받아오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 학생일 경우에는 참가했던 레이스만 입장가능
        // 선생일 경우에는 출제된 레이스에 참가가능
        if ($userData['check']){
            switch ($userData['classification']){
                case 'student':
                    $raceCheck = DB::table('raceUsers as ru')
                        ->select(
                            'ru.retestState as retestState'
                        )
                        ->where([
                            'ru.userNumber' => $userData['userId'],
                            'ru.raceNumber' => $postData['raceId']
                        ])
                        ->first();

                    // 반납할 값 정리
                    if ($raceCheck && ($raceCheck->retestState == 'order')) {
                        $returnValue = array(
                            'sessionId' => $request->session()->get('sessionId'),
                            'raceId' => $postData['raceId'],
                            'check' => true
                        );
                    } else if ($raceCheck){ // 대상자가 아닐경우
                        $returnValue = array(
                            'retestState' => $raceCheck->retestState,
                            'check' => false
                        );
                    } else { // 레이스가 존재하지 않을 경우
                        $returnValue = array(
                            'check' => false
                        );
                    }
                    break;
//                case 'teacher':
//                case 'root':
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

        return view('Race/race_retest')->with('response', $returnValue);
    }

    /****
     * 재시험 문제 받아오기 - 웹은 위의 retestSet부터 실행할 것
     * 
     * @param Request $request->input()
     *      'sessionId' 세션 아이디
     *      'raceId' 레이스 아이디
     * 
     * @return array(
     *      'userName' 유저 이름
     *      'listName' 리스트 이름
     *      'groupName' 그룹 이름
     *      'quizCount' 퀴즈의 수
     *      'passingMark' 넘겨야할 점수
     *      'quizs' => $this->quizGet(리스트 아이디)
     *  )
     */
    public function retestStart(Request $request){
        $postData = array(
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : false,
            'raceId'    => $request->has('raceId') ? $request->input('raceId') : false
        );

        // 유저 정보 받아오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        $raceCheck = DB::table('raceUsers as ru')
            ->select(
                'ru.retestState as retestState',
                'l.number as listId',
                'l.name as listName',
                'u.name as userName',
                'g.name as groupName',
                DB::raw('count(lq.quizNumber) as quizCount'),
                'r.passingMark as passingMark'
            )
            ->where([
                'ru.userNumber' => $userData['userId'],
                'ru.raceNumber' => $postData['raceId']
            ])
            ->join('races as r', 'r.number', '=', 'ru.raceNumber')
            ->join('groups as g', 'g.number', '=', 'r.groupNumber')
            ->join('lists as l', 'l.number', '=', 'r.listNumber')
            ->join('listQuizs as lq', 'lq.listNumber', '=', 'l.number')
            ->join('users as u', 'u.number', '=', 'ru.userNumber')
            ->groupBy(['ru.userNumber', 'ru.raceNumber'])
            ->first();

        if($raceCheck){
            DB::table('sessionDatas')
                ->where([
                    'number' => $postData['sessionId']
                ])
                ->update([
                    'raceNumber' => $postData['raceId']
                ]);

            $retrunValue = array(
                'userName' => $raceCheck->userName,
                'listName' => $raceCheck->listName,
                'groupName' => $raceCheck->groupName,
                'quizCount' => $raceCheck->quizCount,
                'passingMark' => $raceCheck->passingMark,
                'quizs' => $this->quizGet($raceCheck->listId)
            );
        } else {
            $retrunValue = array(
                'check' => false
            );
        }

        return $retrunValue;
    }

    /****
     * 재시험 정답 입력
     *
     * @param Request $request
     *      'sessionId' 세션 아이디
     *      'quizId' 퀴즈 아이디
     *      'answer' 입력한 정답
     *
     * @return array(
     *      'check' 입력 성공 여부
     *  )
     */
    public function retestAnswerIn(Request $request){
        $postData     = array(
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : false,
            'quizId'    => $request->has('quizId') ? $request->input('quizId') : false,
            'answer'    => $request->has('answer') ? $request->input('answer') : false
        );

        // 유저 정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 유저 접속 확인
        if ($userData['check']) {
            // 레이스 정보 가져오기
            $raceData = DB::table('races as r')
                ->select(
                    'r.number as raceId',
                    'r.listNumber as listId'
                )
                ->where([
                    'r.number' => $userData['raceId']
                ])
                ->join('sessionDatas as s', 's.raceNumber', '=', 'r.number')
                ->first();

            // 레이스가 존재할 경우 값을 입력
            if ($raceData && $postData['quizId']) {
                // 정답 미입력 처리
                if (!$postData['answer']) {
                    $postData['answer'] = '';
                }

                // 현재 문제정보
                $quizData = DB::table('quizBanks')
                    ->select(
                        'rightAnswer as right',
                        'type'
                    )
                    ->where([
                        'number' => $postData['quizId']
                    ])
                    ->first();

                switch ($quizData->type) {
                    case 'vocabulary obj':
                    case 'word obj':
                    case 'grammar obj':
                    case 'vocabulary sub':
                    case 'word sub':
                    case 'grammar sub':
                        $rights = explode(',', $quizData->right);
                        $answerCheck = 'X';
                        foreach ($rights as $right) {
                            if ($postData['answer'] == $right) {
                                $answerCheck = 'O';
                                break;
                            }
                        }
                        break;
                    default:
                        $answerCheck = 'X';
                }

                // 정답을 입력
                $quizInsert = DB::table('records')
                    ->insert([
                        'raceNo' => $raceData->raceId,
                        'userNo' => $userData['userId'],
                        'listNo' => $raceData->listId,
                        'quizNo' => $postData['quizId'],
                        'answerCheck' => $answerCheck,
                        'answer' => $postData['answer'],
                        'retest' => 1
                    ]);

                // true 값 입력 성공
                // false 재 시간 이내에 정답 입력실패, 중복입력, 레이스가 없음, 리스트가 없음.
                if ($quizInsert == 1) {
                    $returnValue = array(
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
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 재시험에서 종료 후 세션 정리
     *
     * @param Request $request->input()
     *      'sessionId' 세션 아이디
     *
     * @return array(
     *      'score' 점수
     *      'passingMark' 기준 점수
     *      'check' 종료 성공 여부
     *  )
     */
    public function retestEnd(Request $request){
        $postData = array(
            'sessionId' => $request->input('sessionId')
        );

        // 유저정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        if ($userData['raceId']) {
            // 시험정보 가져오기
            $raceData = DB::table('races as r')
                ->select(
                    'l.name as listName',
                    'r.passingMark as passingMark',
                    'l.number as listId',
                    DB::raw('count(lq.quizNumber) as quizCount')
                )
                ->where([
                    'r.number' => $userData['raceId']
                ])
                ->join('lists as l', 'l.number', '=', 'r.listNumber')
                ->join('listQuizs as lq', 'lq.listNumber', '=', 'l.number')
                ->groupBy('r.number')
                ->first();

            if ($raceData) {
                // 최종 성적 정보 가져오기
                $records = DB::table('records as re')
                    ->select(
                        DB::raw('COUNT(CASE WHEN re.answerCheck = "O" THEN 1 END) as rightCount')
                    )
                    ->where([
                        're.raceNo' => $userData['raceId'],
                        're.userNo' => $userData['userId'],
                        're.retest' => self::RETEST_STATE
                    ])
                    ->groupBy(['re.userNo', 're.raceNo', 're.retest'])
                    ->first();

                // 학생 점수
                $score = (int)(($records->rightCount / $raceData->quizCount) * 100);

                // 합격여부 확인하기
                // 합격
                if ($raceData->passingMark <= $score) {
                    // 미제출 문제 처리하기
                    $this->omission($userData['userId'], $userData['raceId'], self::RETEST_STATE);

                    // 통과 표시하기
                    DB::table('raceUsers')
                        ->where([
                            'raceNumber' => $userData['raceId'],
                            'userNumber' => $userData['userId']
                        ])
                        ->update([
                            'retestState' => 'clear'
                        ]);
                }
                // 불합격
                else {
                    // 시험친 기록 삭제
                    DB::table('records')
                        ->where([
                            'raceNo' => $userData['raceId'],
                            'userNo' => $userData['userId'],
                            'retest' => 1
                        ])
                        ->delete();
                }

                // 반납값 정리
                $returnValue = array(
                    'score' => $score,
                    'passingMark' => $raceData->passingMark,
                    'check' => true
                );
            } else {
                // 반납값 정리
                $returnValue = array(
                    'check' => false
                );
            }
        } else {
            // 반납값 정리
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 해당 리스트에서 모든 문제를 가져오는 구문
     * 
     * @param $listId // 리스트 아이디
     * 
     * @return array(
     *      'quiz' => array(
     *          0 => array(
     *              'quizId' 퀴즈 아이디
     *              'question' 문제
     *              'hint' 힌트
     *              'right' 정답
     *              'example1' 보기1
     *              'example2' 보기2
     *              'example3' 보기3
     *              'quizType' 퀴즈타입
     *              'makeType' 주관식, 객관식
     *          )
     *      ),
     *      'check' 성공 여부
     *  )
     */
    private function quizGet($listId){

        // 문제 가져오기
        $quizData = DB::table('quizBanks as qb')
            ->select([
                'qb.number          as number',
                'qb.question        as question',
                'qb.hint            as hint',
                'qb.rightAnswer     as rightAnswer',
                'qb.example1        as example1',
                'qb.example2        as example2',
                'qb.example3        as example3',
                'qb.type            as type'
            ])
            ->where([
                'lq.listNumber' => $listId
            ])
            ->join('listQuizs as lq', 'lq.quizNumber', '=', 'qb.number')
            ->orderBy('qb.number')
            ->get();

        // 다음 문제가 있을 때
        if($quizData) {

            // 반납값 정리
            $quizs = array();
            foreach ($quizData as $quiz) {
                $type = explode(' ', $quiz->type);
                array_push($quizs, array(
                    'quizId'    => $quiz->number,
                    'question'  => $quiz->question,
                    'hint'      => $quiz->hint,
                    'right'     => $quiz->rightAnswer,
                    'example1'  => $quiz->example1,
                    'example2'  => $quiz->example2,
                    'example3'  => $quiz->example3,
                    'quizType'  => $type[0],
                    'makeType'  => $type[1]
                ));
            }
            $returnValue = array(
                'quiz' => $quizs,
                'check' => true
            );
        } else {
            $returnValue = array('check' => false);
        }

        return $returnValue;
    }

    /****
     * 재시험 대상 레이스 목록을 검색
     *
     * @param $userId // 유저 아이디
     * @return array(
     *      0 => array(
     *          'raceId' 레이스 아이디
     *          'listName' 리스트 이름
     *          'quizCount' 퀴즈의 수
     *          'passingMark' 기준 점수
     *          'rightCount' 기존 점수
     *      )
     *  )
     */
    private function selectRetestList($userId){
        $retestData = DB::table('raceUsers as ru')
            ->select(
                'ru.raceNumber as raceId',
                'l.name as listName',
                DB::raw('count(lq.quizNumber) as quizCount'),
                'r.passingMark as passingMark',
                DB::raw('COUNT(CASE WHEN re.answerCheck = "O" THEN 1 END) as rightCount')
            )
            ->where([
                'ru.userNumber' => $userId,
                'ru.retestState' => 'order'
            ])
            ->join('races as r', 'r.number', '=', 'ru.raceNumber')
            ->join('lists as l', 'l.number', '=', 'r.listNumber')
            ->join('listQuizs as lq', 'lq.listNumber', '=', 'l.number')
            ->join('records as re', function ($join){
                $join->on('re.raceNo', '=', 'ru.raceNumber');
                $join->on('re.userNo', '=', 'ru.userNumber');
            })
            ->groupBy(['ru.raceNumber', 'ru.userNumber'])
            ->orderBy('ru.raceNumber')
            ->get();

        // 레이스번호, 리스트이름, 문항수, 통과점수, 이전점수
        $retests = array();
        foreach ($retestData as $retestRace){
            array_push($retests, array(
                'raceId' => $retestRace->raceId,
                'listName' => $retestRace->listName,
                'quizCount' => $retestRace->quizCount,
                'passingMark' => $retestRace->passingMark,
                'rightCount' => (int)($retestRace->rightCount / $retestRace->quizCount * 100)
            ));
        }

        return $retests;
    }

    /****
     * 재시험 혹은 테스트에서 미제출 문제 처리
     * 
     * @param $userId // 유저 아이디
     * @param $raceId // 레이스 아이디
     * @param $type // 레이스 유형
     */
    private function omission($userId, $raceId, $type){
        $raceData = DB::table('races')
            ->select(
                'listNumber as listId'
            )
            ->where([
                'number' => $raceId
            ])
            ->first();

        $playData = DB::table('listQuizs as lq')
            ->where([
                're.raceNo' => $raceId,
                're.userNo' => $userId,
                're.retest' => $type
            ])
            ->leftJoin('records as re', function ($join) {
                $join->on('re.quizNo', '=', 'lq.quizNumber');
                $join->on('re.listNo', '=', 'lq.listNumber');
            })
            ->pluck('lq.quizNumber')
            ->toArray();

        $quizData = DB::table('listQuizs')
            ->select(
                'quizNumber as quizId'
            )
            ->where([
                'listNumber' => $raceData->listId
            ])
            ->whereNotIn('quizNumber', $playData)
            ->get();

        $insert = array();
        foreach ($quizData as $quiz) {
            array_push($insert, array(
                'userNo' => $userId,
                'raceNo' => $raceId,
                'listNo' => $raceData->listId,
                'quizNo' => $quiz->quizId,
                'retest' => $type,
                'answer' => '',
                'answerCheck' => 'X'
            ));
        }

        if (count($insert) > 0) {
            DB::table('records')
                ->insert($insert);
        }
    }
}