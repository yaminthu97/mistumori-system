<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Question;

class InquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $inquiriesData = [
                [
                    'id' => 1,
                    'project_id' => 1,
                    'comment_content' => 'Does this project need caching system?',
                    'expected_answer_date' => '2023-07-17',
                    'priority' => 2,
                    'question_assignee' => 2,
                    'created_user_id'  => 1,
                ],
                [
                    'id' => 2,
                    'project_id' => 2,
                    'comment_content' => 'How many developer do you use in this project?',
                    'expected_answer_date' => '2023-07-18',
                    'priority' => 1,
                    'status' => 1,
                    'question_assignee' => 1,
                    'created_user_id'  => 2,
                ],
                [
                    'id' => 3,
                    'project_id' => 3,
                    'comment_content' => 'SQL or NoSQL?',
                    'expected_answer_date' => '2023-08-17',
                    'priority' => 0,
                    'status' => 2,
                    'question_assignee' => 3,
                    'created_user_id'  => 1,
                ],
                [
                    'id' => 4,
                    'project_id' => 1,
                    'comment_content' => 'Which package system is used?',
                    'expected_answer_date' => '2023-07-17',
                    'priority' => 2,
                    'status' => 1,
                    'question_assignee' => 2,
                    'created_user_id'  => 1,
                ],
                [
                    'id' => 14,
                    'project_id' => 2,
                    'comment_content' => 'question is answer?',
                    'expected_answer_date' => '2023-07-19',
                    'priority' => 2,
                    'status' => 0,
                    'question_assignee' => 1,
                    'created_user_id'  => 2,
                ],
            ];

            foreach ($inquiriesData as $data) {
                $inquiries = new Question();
                $inquiries->fill($data)->save();
            }
        });
    }
}
