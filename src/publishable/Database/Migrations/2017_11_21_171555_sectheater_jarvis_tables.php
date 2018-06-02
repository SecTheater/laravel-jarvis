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
            $table->string('job');
            $table->boolean('hire')->default(false);
            $table->text('permissions')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('title')->unique();
            $table->text('body');
            $table->string('image')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            if (config('jarvis.posts.approve')) {
                $table->boolean('approved')->default(0);
                $table->integer('approved_by')->unsigned()->nullable();
                $table->date('approved_at')->nullable();
            }
            $table->timestamps();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            if (config('jarvis.posts.approve')) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            }
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('admin_id');
            $table->timestamps();
        });
        Schema::create('post_tag', function (Blueprint $table) {
            $table->integer('post_id');
            $table->integer('tag_id');
            $table->primary(['post_id', 'tag_id']);
        });
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('post_id')->unsigned();
            if (config('jarvis.comments.approve')) {
                $table->boolean('approved')->default(0);
                $table->date('approved_at')->nullable();
                $table->integer('approved_by')->unsigned()->nullable();
            }
            $table->text('body');
            $table->timestamps();
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
            $table->boolean('status')->default(0); // if using dislike and like, unliked -> 0 , liked -> 1
            $table->string('likable_type');
            $table->integer('likable_id')->unsigned();
            $table->timestamps();
        });
        Schema::table('likes',function(Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('permissions')->nullable("{}");
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
            $table->timestamp('completed_at');
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('posts');
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            if (config('jarvis.posts.approve')) {
                $table->dropForeign('approved_by')->references('id')->on('users')->onDelete('cascade');
            }
        });

        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_tag');
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            if (config('jarvis.comments.approve')) {
                $table->dropForeign('approved_by')->references('id')->on('users')->onDelete('cascade');
            }
        });
        Schema::dropIfExists('comments');
        Schema::table('replies', function (Blueprint $table) {
            $table->dropForeign(['post_id', 'user_id', 'comment_id']);
            if (config('jarvis.replies.approve')) {
                $table->dropForeign('approved_by')->references('id')->on('users')->onDelete('cascade');
            }
        });
        Schema::dropIfExists('replies');
        Schema::dropIfExists('likes');
        Schema::table('role_users', function (Blueprint $table) {
            $table->dropForeign(['user_id', 'role_id']);
        });
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_users');
        Schema::table('activations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('activations');
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('reminders');
    }
}
