<?php

namespace App\Http\Controllers\RoleManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleManagement\StoreRoleRequest;
use App\Http\Requests\RoleManagement\UpdateRoleRequest;
use App\Models\RoleManagement\Role;
use App\Services\RoleService;
use Config;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class RoleController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RoleService $roleService)
    {
        $this->middleware('permission:list-role');
        $this->middleware('permission:create-role', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-role', ['only' => ['destroy']]);
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->roleService->getRoleData();
            return $this->initDataTable($data);
        } else {
            return view('pages.rolemanagement.roles.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $statusArr = Config::get('params.status');
        $modules = $this->roleService->getActiveModules();
        return view('pages.rolemanagement.roles._add', compact('statusArr', 'modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRoleRequest $request
     * @return Response
     */
    public function store(StoreRoleRequest $request, Role $role)
    {
        $this->roleService->storeRole($request);
        return redirect()->route('role.index')->with('success', trans('admin.message.created', ['module' => trans('admin.label.role')]));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Role $role
     * @return Response
     */
    public function edit(string $uuid)
    {

        $statusArr = Config::get('params.status');
        $modules = $this->roleService->getActiveModules();
        $role = $this->roleService->getRole($uuid);
        if ($role->has('permissions')) {
            $permissions = [];
            foreach ($role->permissions as $value) {
                $permissions[] = $value->id;
            }
        }
        return view('pages.rolemanagement.roles._edit', compact('role', 'modules', 'permissions', 'statusArr'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRoleRequest $request
     * @param Role $role
     * @return Response
     */
    public function update(UpdateRoleRequest $request, string $uuid)
    {
        $this->roleService->updateRole($request, $uuid);
        return redirect()->route('role.index')->with('success', trans('admin.message.updated', ['module' => trans('admin.label.role')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return Response
     */
    public function destroy(string $uuid)
    {
        $this->roleService->deleteRole($uuid);
        return redirect()->route('role.index')->with('success', trans('admin.message.deleted', ['module' => trans('admin.label.role')]));
    }
    /**
     * initDataTable function
     *
     * @param object $data
     * @return object
     */
    public function initDataTable(object $data)
    {

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return view('pages.rolemanagement.roles._action-menu', compact('data'));
            })
            ->editColumn('name', function ($data) {
                return '<a href="' . route('role.edit', $data->uuid) . '">' . $data->name . '</a>';
            })
            ->editColumn('status', function ($data) {
                return $data->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">In-active</span>';
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('d/m/Y H:i:s');
            })
            ->escapeColumns([])
            ->make(true);

    }
}