<?php

namespace App\Http\Controllers\RoleManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleManagement\StorePermissionRequest;
use App\Models\RoleManagement\Module;
use App\Models\RoleManagement\Permission;
use App\Services\ModuleService;
use App\Services\PermissionService;
use Config;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class PermissionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->middleware('permission:list-permission');
        $this->middleware('permission:create-permission', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-permission', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-permission', ['only' => ['destroy']]);
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->permissionService->getPermissionData();
            return $this->initDataTable($data);
        } else {
            return view('pages.rolemanagement.permissions.index');
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
        $modules = (new ModuleService())->getModuleData()->get();
        return view('pages.rolemanagement.permissions._add', compact('statusArr', 'modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePermissionRequest $request
     * @return Response
     */
    public function store(StorePermissionRequest $request)
    {
        $this->permissionService->storePermission($request);
        return redirect()->route('permission.index')->with('success', trans('admin.message.created', ['module' => trans('admin.label.permission')]));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Permission $permission
     * @return Response
     */
    public function edit(string $uuid)
    {
        $statusArr = Config::get('params.status');
        $modules = (new ModuleService())->getModuleData()->get();
        $permission = $this->permissionService->getPermission($uuid);
        return view('pages.rolemanagement.permissions._edit', compact('permission', 'statusArr', 'modules'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StorePermissionRequest $request
     * @param Permission $permission
     * @return Response
     */
    public function update(StorePermissionRequest $request, string $uuid)
    {
        $this->permissionService->updatePermission($request, $uuid);
        return redirect()->route('permission.index')->with('success', trans('admin.message.updated', ['module' => trans('admin.label.permission')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Permission $permission
     * @return Response
     */
    public function destroy(string $uuid)
    {
        $this->permissionService->deletePermission($uuid);
        return redirect()->route('permission.index')->with('success', trans('admin.message.deleted', ['module' => trans('admin.label.permission')]));
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
                return view('pages.rolemanagement.permissions._action-menu', compact('data'));
            })
            ->editColumn('name', function ($data) {
                return '<a href="' . route('permission.edit', $data->uuid) . '">' . $data->name . '</a>';
            })
            ->editColumn('module.name', function ($data) {
                return $data->module !== null ? $data->module->name : '';
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
