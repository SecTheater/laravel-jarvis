<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SectheaterJarvisTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('password');
            $table->string('sec_question');
            $table->string('sec_answer');
            $table->string('location');
            $table->date('dob');
            $table->text('permissions')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('title')->unique();
            $table->text('body');
            if (config('jarvis.posts.approve')) {
                $table->boolean('approved')->default(0);
                $table->integer('approved_by')->unsigned()->nullable();
                $table->date('approved_at')->nullable();
            }
            $table->timestamps();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            if (config('jarvis.posts.approve')) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            }
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('post_tag', function (Blueprint $table) {
            $table->integer('post_id');
            $table->integer('tag_id');
            $table->primary(['post_id', 'tag_id']);
        });
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            if (config('jarvis.comments.approve')) {
                $table->boolean('approved')->default(0);
                $table->date('approved_at')->nullable();
                $table->integer('approved_by')->unsigned()->nullable();
            }
            $table->text('body');
            $table->integer('post_id')->unsigned();
            $table->timestamps();
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            if (config('jarvis.comments.approve')) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            }
        });
        Schema::create('replies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('comment_id')->unsigned();
            $table->integer('post_id')->unsigned();
            $table->text('body');
            if (config('jarvis.replies.approve')) {
                $table->boolean('approved')->default(0);
                $table->integer('approved_by')->unsigned()->nullable();
                $table->date('approved_at')->nullable();
            }
            $table->timestamps();
        });
        Schema::table('replies', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            if (config('jarvis.replies.approve')) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            }
        });
        Schema::create('likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('likable_type');
            $table->integer('likable_id');
            $table->boolean('like_status');
            $table->timestamps();
        });
        Schema::table('likes', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('permissions')->nullable();
            $table->timestamps();
        });
        Schema::create('role_users', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->primary(['user_id', 'role_id']);
        });
        Schema::table('role_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
        Schema::create('activations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('token')->nullable();
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::table('activations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::create('reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->boolean('completed')->default(0);
            $table->string('token')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::table('reminders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            if (config('jarvis.posts.approve')) {
                if (Schema::hasColumn('posts', 'approved_by')) {
                    $table->dropForeign(['approved_by']);
                }
            }
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            if (config('jarvis.comments.approve')) {
                if (Schema::hasColumn('comments', 'approved_by')) {
                    $table->dropForeign(['approved_by']);
                }
            }
        });
        Schema::table('replies', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['comment_id']);
            if (config('jarvis.replies.approve')) {
                if (Schema::hasColumn('replies', 'approved_by')) {
                    $table->dropForeign(['approved_by']);
                }
            }
        });
        Schema::table('role_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['role_id']);
        });
        Schema::table('activations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('posts');

        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_tag');

        Schema::dropIfExists('comments');

        Schema::dropIfExists('replies');
        Schema::dropIfExists('likes');

        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_users');

        Schema::dropIfExists('activations');

        Schema::dropIfExists('reminders');
        Schema::dropIfExists('users');
    }
}
