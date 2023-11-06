<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * 2038年問題対応
     *  デフォルトのuse指定を以下のように変更する
     *  use database\Blueprint;
     *  use App\Facades\Schema;
     *
     *  $table->timestamps()をdateTime型で作成するdateTimes()が利用できる
     *  $table->softDeletes()をdateTime型で作成するdateTimes()が利用できる
     *
     * Command
     *   migrate 実行
     *     > php artisan migrate
     *   migrate ロールバック
     *     > php artisan migrate:rollback
     *   migrate ロールバック & 再構築 & 初期データ投入
     *     > php artisan migrate:refresh --seed
     *
     * @return void
     */
    public function up()
    {
        // 管理画面アカウント
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('role')->unsigned()->default(1);
            $table->string('login_id', 255);
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('password', 255);
            $table->integer('created_user_id');
            $table->integer('updated_user_id')->nullable();
            $table->timestamps();
        });

        // お客様
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_name', 255);
            $table->string('description', 255);
            $table->tinyInteger('status')->unsigned()->default(1);
            $table->foreignId('created_user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->foreignId('updated_user_id')->references('id')->on('users')->constrained()->onDelete('cascade')->nullable();
            $table->timestamps();
        });

        // 計画
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('project_name', 255);
            $table->foreignId('customer_id')->references('id')->on('customers')->constrained()->onDelete('cascade');
            $table->tinyInteger('project_type')->unsigned();
            $table->string('system_overview', 1000);
            $table->string('system_overview_file_path', 255)->nullable();
            $table->string('phases', 255);
            $table->string('language', 255);
            $table->string('server_env', 255)->nullable();
            $table->date('expected_dev_start_date')->nullable();
            $table->date('expected_dev_end_date')->nullable();
            $table->date('expected_submit_date');
            $table->tinyInteger('priority')->unsigned();
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->foreignId('assignee')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->foreignId('created_user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->foreignId('updated_user_id')->references('id')->on('users')->constrained()->onDelete('cascade')->nullable();
            $table->timestamps();
        });

        // コメント
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->references('id')->on('projects')->constrained()->onDelete('cascade');
            $table->string('comment_content', 1000);
            $table->foreignId('comment_assignee')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->foreignId('created_user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });

        // 推定
        Schema::create('estimations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->references('id')->on('projects')->constrained()->onDelete('cascade');
            $table->string('estimation_content', 1000);
            $table->string('estimation_file_path', 255)->nullable();
            $table->decimal('PG_man_months', 2, 1);
            $table->decimal('BSE_man_months', 2, 1);
            $table->foreignId('created_user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });

        // 質問
        Schema::create('questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->references('id')->on('projects')->constrained()->onDelete('cascade');
            $table->string('comment_content', 1000);
            $table->date('expected_answer_date');
            $table->tinyInteger('priority')->unsigned();
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->foreignId('question_assignee')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->foreignId('created_user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });

        // 答え
        Schema::create('answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('question_id')->references('id')->on('questions')->constrained()->onDelete('cascade');
            $table->string('answer_content', 1000);
            $table->foreignId('created_user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('estimations');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('users');
    }
};
