<?php

namespace App\Http\Controllers\Admin\api\v1\role;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\api\v1\role\RoleResource;
use App\Repositories\Admin\api\v1\role\RoleRepository;
use App\Http\Resources\Admin\api\v1\role\RoleCollection;
use App\Http\Requests\Admin\api\v1\role\StoreRoleRequest;
use App\Http\Requests\Admin\api\v1\role\UpdateRoleRequest;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->middleware('permission:role-list|role-create|role-edit|role-delete');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = $this->roleRepository->getAll();
        return new RoleCollection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleRepository->createRoleWithPermissions($request->validated());
        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role = $this->roleRepository->get($role);
        return new RoleResource($role->load('permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role = $this->roleRepository->updateRoleWithPermissions($role, $request->validated());
        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role = $this->roleRepository->delete($role);
        return new RoleResource($role);
    }
}
