<?php
namespace SecTheater\Jarvis\Observers;
use Illuminate\Database\Eloquent\Model;

class ReplyObserver {
	public function creating(Model $reply) {
		$reply->user_id = user()->id;
		if (config('jarvis.replies.approve') && user()->hasAnyRole(['*.replies.approve'])) {
			$reply->approved    = true;
			$reply->approved_by = user()->id;
			$reply->approved_at = date('Y-m-d H:i:s');
			$reply->updated_at  = null;
		}
	}
	public function updating(Model $reply) {
		if (config('jarvis.replies.approve') && user()->hasAnyRole(['*.replies.approve'])) {
			$reply->approved    = true;
			$reply->approved_by = user()->id;
			$reply->approved_at = date('Y-m-d H:i:s');
			$reply->updated_at  = date('Y-m-d H:i:s');
		} elseif (config('jarvis.replies.approve') && !user()->hasAnyRole(['*.replies.approve'])) {
			$reply->approved    = false;
			$reply->approved_by = null;
			$reply->approved_at = null;
			$reply->updated_at  = date('Y-m-d H:i:s');
		}

	}
}