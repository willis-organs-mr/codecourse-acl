<?php 

namespace App\Permissions;

use App\{Role, Permission};

trait HasPermissionsTrait
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }

    public function givePermissionTo(...$permissions)
    {
        $permissions = $this->getPermission(array_flatten($permissions));
        
        if ($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    public function updatePermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions);
    }

    public function togglePermission(...$permissions)
    {
        $permissions = $this->getPermission(array_flatten($permissions));

        $this->permissions()->toggle($permissions);

        return $this;
    }

    public function withdrawPermissionFrom(...$permissions)
    {
        $permissions = $this->getPermission(array_flatten($permissions));

        $this->permissions()->detach($permissions);

        return $this;
    }

    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }

        return false;
    }

    protected function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }

        return false;
    }

    public function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    protected function hasPermission($permission)
    {
        return !! $this->permissions->where('name', $permission->name)->count();
    }

    protected function getPermission(array $permissions)
    {
        return Permission::whereIn('name', $permissions)->get();
    }
}
