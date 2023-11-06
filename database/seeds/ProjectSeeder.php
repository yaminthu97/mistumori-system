<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {

            $projectsData = [
                [
                    'id' => 1,
                    'project_name' => 'Project 1',
                    'customer_id' => 1,
                    'project_type' => 1,
                    'system_overview' => 'Web project, content management system, CMS',
                    'phases' => 'Strategy formulation',
                    'language' => 'PHP',
                    'server_env' => 'Apache',
                    'expected_dev_start_date' => '2023-08-14',
                    'expected_dev_end_date' => '2023-10-01',
                    'expected_submit_date' => '2023-07-20',
                    'priority' => 2,
                    'assignee' => 1,
                    'created_user_id' => 2,
                    'updated_user_id' => 1,
                ],
                [
                    'id' => 2,
                    'project_name' => 'Project 2',
                    'customer_id' => 2,
                    'project_type' => 2,
                    'system_overview' => 'System development, point of sale, POS',
                    'phases' => 'Manufacturing',
                    'language' => 'C#',
                    'server_env' => 'Nginx',
                    'expected_dev_start_date' => '2023-07-24',
                    'expected_dev_end_date' => '2023-10-01',
                    'expected_submit_date' => '2023-07-21',
                    'priority' => 1,
                    'assignee' => 1,
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ],
                [
                    'id' => 3,
                    'project_name' => 'Project 3',
                    'customer_id' => 3,
                    'project_type' => 3,
                    'system_overview' => 'Web System, Enterprise resource planning, ERP',
                    'phases' => 'Maintenance',
                    'language' => 'Python',
                    'expected_submit_date' => '2023-07-10',
                    'priority' => 0,
                    'assignee' => 3,
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ],
            ];

            foreach ($projectsData as $data) {
                $projects = new Project();
                $projects->fill($data)->save();
            }
        });
    }
}
