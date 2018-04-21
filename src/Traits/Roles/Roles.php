<?php

namespace SecTheater\Jarvis\Traits\Roles;

use SecTheater\Jarvis\Exceptions\InsufficientPermissionsException;

trait Roles
{
    protected $permissions;
    protected $user;

    public function hasAllRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (!$this->hasRole($role)) {
                    return false;
                }
            }
        } else {
            if (!$this->hasRole($roles)) {
                return false;
            }
        }

        return true;
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }

        return false;
    }

    public function hasRole($role)
    {
        $this->user = $this;
        $roles = $this->user->roles()->first()->permissions;
        $secondary_roles = $this->user->toArray()['permissions'] ?? [];
        if (array_key_exists($role, $roles) && $roles[$role] === true) {
            return true;
        }
        if (array_key_exists($role, $secondary_roles) && $secondary_roles[$role] === true) {
            return true;
        }
        foreach ($roles as $key => $value) {
            if ((str_is($role, $key) || str_is($key, $role)) && $value === true) {
                return true;
            }
        }
        foreach ($secondary_roles as $key => $value) {
            if ((str_is($role, $key) || str_is($key, $role)) && $value === true) {
                return true;
            }
        }

        return false;
    }

    public function addPermission($permission, $value = true)
    {
        $permissions = $this->getPermissions() ?? [];
        if (is_array($permission)) {
            foreach ($permission as $key => $val) {
                if (!array_key_exists($key, $permissions)) {
                    $permissions = array_merge($permissions, [$key => $val]);
                }
            }

            return $this->setPermissions($this, $permissions);
        } elseif (is_string($permission)) {
            if (!array_key_exists($permission, $permissions)) {
                $permissions = array_merge($permissions, [$permission => $value]);

                return $this->setPermissions($this, $permissions);
            }
        }

        return false;
    }

    public function updatePermission($permission, $value = true, $create = false)
    {
        $permissions = $this->getPermissions() ?? [];
        if (array_key_exists($permission, $permissions)) {
            $permissions[$permission] = $value;

            return $this->setPermissions($this->user ?? $this, $permissions);
        } elseif ($create) {
            if ($this->addPermission($permission, $value)) {
                return true;
            }
        }

        return false;
    }

    public function removePermission(...$permission)
    {
        $permissions = $this->getPermissions() ?? [];
        if (count($permissions) === 0) {
            return false;
        }

        foreach ($permission as $key) {
            if (!isset($permissions[$key])) {
                throw new InsufficientPermissionsException("$key Permission Does not exist for ".$this->username, 404);
            }
            if (array_key_exists($key, $permissions)) {
                unset($permissions[$key]);
            }
        }
        if (count($permissions) === 0) {
            $permissions = null;
        }

        return $this->setPermissions($this, $permissions);
    }

    public function setPermissions($user, $permissions)
    {
        return $user->update(['permissions' => $permissions]);
    }

    public function getPermissions()
    {
        $permissions = $this->toArray()['permissions'] ?? $this->user->toArray()['permissions'];

        return is_string($permissions) ? json_decode($permissions, true) : $permissions;
    }

    public function inRole($slug)
    {
        return  $this->roles->first()->slug === $slug;
    }
}
