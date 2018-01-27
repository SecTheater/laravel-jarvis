<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		DB::table('roles')->insert(array(
				array('slug' => 'admin', 'name' => 'Administrator', 'permissions' => '{"admin.posts.show":true,"admin.posts.edit":true,"admin.posts.delete":true,"admin.posts.create":true,"admin.posts.approve":true,"admin.tags.create":true,"admin.tags.edit":true,"admin.tags.delete":true,"admin.tags.show":true,"admin.comments.show":true,"admin.comments.create":true,"admin.comments.edit":true,"admin.comments.delete":true,"admin.comments.approve":true,"admin.replies.create":true,"admin.replies.edit":true,"admin.replies.delete":true,"admin.replies.approve":true,"admin.replies.show":true,"admin.likes.show":true,"admin.likes.edit":true,"admin.likes.create":true,"admin.likes.delete":true,"admin.users.upgrade":true,"admin.users.downgrade":true,"admin.tasks.approve":true}'),
				array('slug' => 'moderator', 'name' => 'moderator', 'permissions' => '{"moderator.posts.show":true,"moderator.posts.edit":true,"moderator.posts.delete":true,"moderator.posts.create":true,"moderator.posts.approve":false,"moderator.tags.create":true,"moderator.tags.edit":true,"moderator.tags.delete":true,"moderator.tags.show":true,"moderator.comments.show":true,"moderator.comments.create":true,"moderator.comments.edit":true,"moderator.comments.delete":true,"moderator.comments.approve":false,"moderator.replies.create":true,"moderator.replies.edit":true,"moderator.replies.delete":true,"moderator.replies.approve":false,"moderator.replies.show":true,"moderator.likes.show":true,"moderator.likes.edit":true,"moderator.likes.create":true,"moderator.likes.delete":true,"moderator.users.upgrade":false,"moderator.users.downgrade":false}'),
				array('slug' => 'user', 'name' => 'Normal User', 'permissions' => '{"user.posts.show":true,"user.posts.edit":false,"user.posts.delete":false,"user.posts.create":false,"user.posts.approve":false,"user.tags.create":false,"user.tags.edit":false,"user.tags.delete":false,"user.tags.show":true,"user.comments.show":true,"user.comments.create":true,"user.comments.edit":true,"user.comments.delete":true,"user.comments.approve":false,"user.replies.create":true,"user.replies.edit":true,"user.replies.delete":true,"user.replies.approve":false,"user.replies.show":true,"user.likes.show":true,"user.likes.edit":true,"user.likes.create":true,"user.likes.delete":true}'),
			));
	}
}
