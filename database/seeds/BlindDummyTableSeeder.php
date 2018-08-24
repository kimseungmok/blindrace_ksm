<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlindDummyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 유저 정보
        $users = [
            [123456789, 'sub', 'チェ教師', 'teacher'],
            [999999999, 'main', 'ソ教師', 'root'],
            [1301282, '1234', 'チェ・ビョンチャン', 'student'],
            [1601145, '1234', 'シム・ユリム', 'student'],
            [1301143, '1234', 'ソン・ヒョンソク', 'student'],
            [1401179, '1234', 'アン・ジュンフイ', 'student'],
            [1401055, '1234', 'キム・スンモク', 'student'],
            [1301036, '1234', 'キム・ミンス', 'student'],
            [1, '1', 'チ', 'student'],
            [2, '2', 'シ', 'student'],
            [3, '3', 'ソ', 'student'],
            [4, '4', 'ア', 'student'],
            [5, '5', 'ム', 'student'],
            [6, '6', 'キ', 'student']
        ];
        foreach ($users as $user) {
            DB::table('users')->insert([
                'number' => array_get($user, 0),
                'pw' => array_get($user, 1),
                'name' => array_get($user, 2),
                'classification' => array_get($user, 3)
            ]);
        }

        $userss = [
            [2000005,'1234','ヤン・ナラ','student'],
            [2000007,'1234','クォン・ネイル','student'],
            [2000011,'1234','チョン・ミョンジェ','student'],
            [2000014,'1234','パク・バダ','student'],
            [2000023,'1234','イ・ヨジョン','student']
        ];
        foreach($userss as $user){
            DB::table('users')->insert([
                'number'            => array_get($user, 0),
                'pw'                => array_get($user, 1),
                'name'              => array_get($user, 2),
                'classification'    => array_get($user, 3)
            ]);
        }
        
        // 그룹 정보
        $groupId = DB::table('groups')->insertGetId([
            'name' => '3WDJ特講Bクラス',
            'teacherNumber' => $users[0][0]
        ], 'number');

        // 그룹에 학생 추가
        for ($number = 2; $number < count($users); $number++) {
            DB::table('groupStudents')->insert([
                'groupNumber' => $groupId,
                'userNumber' => $users[$number][0],
            ]);
        }

        // 그룹 정보
        $groupId = DB::table('groups')->insertGetId([
            'name' => '3WDJ正規Aクラス',
            'teacherNumber' => $users[0][0]
        ], 'number');

        // 그룹에 학생 추가
        for ($number = 2; $number < count($users); $number++) {
            DB::table('groupStudents')->insert([
                'groupNumber' => $groupId,
                'userNumber' => $users[$number][0],
            ]);
        }

        // 기본 리스트 폴더 생성
        $folderId = DB::table('folders')->insertGetId([
            'teacherNumber' => $users[0][0],
            'name' => "テスト用フォルダ1",
        ], 'number');

        // 이미지 등록
        for ($char = 1; $char <= 45; $char++) {
            DB::table('characters')->insert([
                'url' => 'img/character/char' . (string)$char
            ]);
        }

        // 교재 정보
        $bookId = DB::table('books')->insertGetId([
            'name' => "急所攻略N1",
            'maxPage' => 12,
            'minPage' => 195
        ], 'number');

        // 교재의 문제 정보
        $quizs = array(
            [
                'page'          => 17,
                'type'          => 'vocabulary',
                'quizType'      => 'obj',
                'question'      => 'その路線バスは（頻繁）に出ている。',
                'rightAnswer'   => 'ひんぱん',
                'example'       => ['びんしょう', 'ひんはん', 'びんじょう'],
                'level'         => '1'
            ],
            [
                'page'          => 17,
                'type'          => 'vocabulary',
                'quizType'      => 'sub',
                'question'      => '各ビール会社がシェアを（競って）いる。',
                'rightAnswer'   => 'きそって',
                'example'       => ['たたかって', 'あせって', 'ちかって'],
                'hint'          => 'OOoO',
                'level'         => '1'
            ],
            [
                'page'          => 18,
                'type'          => 'word',
                'quizType'      => 'obj',
                'question'      => '日本語',
                'rightAnswer'   => 'にほんご',
                'example'       => ['にぽんご', 'にちほんご', 'げつほんご'],
                'level'         => '1'
            ],
            [
                'page'          => 18,
                'type'          => 'word',
                'quizType'      => 'sub',
                'question'      => '開発計画',
                'rightAnswer'   => 'かいはつけいかく',
                'example'       => ['がいはつけいかく', 'かいはつげいかく', 'がいはつげいかく'],
                'hint'          => '濁音に注意',
                'level'         => '1'
            ],
            [
                'page'          => 19,
                'type'          => 'grammar',
                'quizType'      => 'obj',
                'question'      => 'あの作家は小説を書く（　　）絵も描いている。',
                'rightAnswer'   => 'かたわら',
                'example'       => ['がてら', 'ところを', 'にたら'],
                'level'         => '1'
            ],
            [
                'page'          => 19,
                'type'          => 'grammar',
                'quizType'      => 'sub',
                'question'      => '事情の（　　）を問わず、提出期限に遅れた卒業論文は受け付けない。',
                'rightAnswer'   => 'いかん',
                'example'       => ['しまつ', 'ゆえ', 'すききらい'],
                'hint'          => '',
                'level'         => '1'
            ]
        );

        $listId = DB::table('lists')->insertGetId([
            'folderNumber' => $folderId,
            'name' => "テスト用リスト1",
            'openState' => 1
        ], 'number');

        $quizList = array();
        foreach ($quizs as $quiz) {
            if($quiz['quizType'] == 'obj') {
                $quizId = DB::table('quizBanks')
                    ->insertGetId([
                        'bookNumber' => $bookId,
                        'page' => $quiz['page'],
                        'question' => $quiz['question'],
                        'rightAnswer' => $quiz['rightAnswer'],
                        'example1' => $quiz['example'][0],
                        'example2' => $quiz['example'][1],
                        'example3' => $quiz['example'][2],
                        'type' => $quiz['type'] . ' ' . $quiz['quizType'],
                        'level' => $quiz['level']
                    ], 'number');
            } else {
                $quizId = DB::table('quizBanks')
                    ->insertGetId([
                        'bookNumber' => $bookId,
                        'page' => $quiz['page'],
                        'question' => $quiz['question'],
                        'hint' => $quiz['hint'],
                        'rightAnswer' => $quiz['rightAnswer'],
                        'type' => $quiz['type'] . ' ' . $quiz['quizType'],
                        'level' => $quiz['level']
                    ], 'number');
            }

            array_push($quizList, $quizId);

            DB::table('listQuizs')->insert([
                'listNumber' => $listId,
                'quizNumber' => $quizId
            ]);
        }

        // 오답, 재시험 상태
        $state = array(
            'not',
            'order',
            'clear'
        );

        // 1주일 결과 정보
        for ($day = 7 ; $day > 0 ; $day--) {
            $raceId = DB::table('races')->insertGetId([
                'groupNumber' => $groupId,
                'teacherNumber' => $users[0][0],
                'listNumber' => $listId,
                'type' => 'race',
                'created_at' => DB::raw('subdate(now(), INTERVAL ' . $day . ' DAY)')
            ], 'number');

            $check = true;
            for ($number = 2; $number < count($users); $number++) {
                DB::table('raceUsers')
                    ->insert([
                        'raceNumber' => $raceId,
                        'userNumber' => $users[$number][0],
                        'retestState' => $state[0],
                        'wrongState' => $state[0]
                    ]);

                $retestState = 0;
                for ($quizCount = 1; $quizCount <= 6; $quizCount++) {
                    $answerCheck = (mt_rand(0, $quizCount + $number) < $day / 2) ? 'X' : 'O';
                    $answer = ($answerCheck == 'O') ? $quizs[$quizCount - 1]['rightAnswer'] : $quizs[$quizCount - 1]['example'][($number + $quizCount + $day) % 3];

                    DB::table('records')->insert([
                        'raceNo' => $raceId,
                        'userNo' => $users[$number][0],
                        'listNo' => $listId,
                        'quizNo' => $quizList[$quizCount - 1],
                        'answerCheck' => $answerCheck,
                        'answer' => $answer
                    ]);

                    if ($answerCheck == 'X') {
                        $retestState++;
                    }
                }

                // 재시험 결과 정보
                if ($retestState > 2 && (mt_rand(0, 2) != 0)) {
                    do {
                        $retestState = 0;
                        $insert = array();

                        for ($quizCount = 1; $quizCount <= 6; $quizCount++) {
                            $answerCheck = (mt_rand(0, $quizCount + $number) < $day / 4) ? 'X' : 'O';
                            $answer = ($answerCheck == 'O') ? $quizs[$quizCount - 1]['rightAnswer'] : $quizs[$quizCount - 1]['example'][($number + $quizCount + $day) % 3];
                            array_push($insert, array(
                                'raceNo' => $raceId,
                                'userNo' => $users[$number][0],
                                'listNo' => $listId,
                                'quizNo' => $quizList[$quizCount - 1],
                                'answerCheck' => $answerCheck,
                                'answer' => $answer,
                                'retest' => 1
                            ));

                            if ($answerCheck == 'X') {
                                $retestState++;
                            }
                        }
                    }while($retestState <= 2);

                    DB::table('records')->insert($insert);

                    DB::table('raceUsers')
                        ->where([
                            'raceNumber' => $raceId,
                            'userNumber' => $users[$number][0]
                        ])
                        ->update([
                            'retestState' => $state[2],
                            'wrongState' => $state[1]
                        ]);
                } else if($retestState > 2){
                    DB::table('raceUsers')
                        ->where([
                            'raceNumber' => $raceId,
                            'userNumber' => $users[$number][0]
                        ])
                        ->update([
                            'retestState' => $state[1],
                            'wrongState' => $state[1]
                        ]);
                } else if($retestState > 0){
                    DB::table('raceUsers')
                        ->where([
                            'raceNumber' => $raceId,
                            'userNumber' => $users[$number][0]
                        ])
                        ->update([
                            'retestState' => $state[0],
                            'wrongState' => $state[1]
                        ]);
                }
            }
        }

        DB::table('QnAs')
            ->insert([
                'userNumber' => 1301282,
                'teacherNumber' => 123456789,
                'groupNumber' => 1,
                'title' => 'すくすくレースに質問があります。',
                'question' => '私の考えには3番が正解なのにどうして間違えたんですか',
                'questionFileNumber' => null
            ]);
    }
}