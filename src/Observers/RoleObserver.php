<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class RoleObserver
{

    public function creating(Model $role)
    {
        $role->created_at = date('Y-m-d H:i:s');
        $role->updated_at = null;
    }

    public function updating(Model $role)
    {
        $role->updated_at = date('Y-m-d H:i:s');
    }

    public function deleting(Model $role)
    {
        $role->users()->detach();
    }
}
