<?php

namespace App\Http\Controllers\RoleManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleManagement\UpdateUserRequest;
use App\Models\RoleManagement\Role;
use App\Models\User;
use App\Services\UserService;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('permission:list-user');
        $this->middleware('permission:create-user', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);
        // $this->middleware('check-access:list-user');
        // $this->middleware('check-access:create-user', ['only' => ['create', 'store']]);
        // $this->middleware('check-access:edit-user', ['only' => ['edit', 'update']]);
        // $this->middleware('check-access:delete-user', ['only' => ['destroy']]);

        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->userService->getUserData();
            return $this->initDataTable($data);
        } else {
            return view('pages.rolemanagement.users.index');
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
        $genders = Config::get('params.gender');
        $masterCountry = $this->userService->getActiveCountries();
        $roles = $this->userService->getActiveRoles();
        return view('pages.rolemanagement.users._add', compact('statusArr', 'roles', 'genders', 'masterCountry'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UpdateUserRequest $request
     * @return Response
     */
    public function store(UpdateUserRequest $request)
    {
        $this->userService->storeUser($request);
        return redirect()->route('user.index')->with('success', trans('admin.message.created', ['module' => trans('admin.label.user')]));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return Response
     */
    // public function edit(User $user)
    public function edit(string $uuid)
    {
        $statusArr = Config::get('params.status');
        $genders = Config::get('params.gender');
        $masterCountry = $this->userService->getActiveCountries();
        $roles = $this->userService->getActiveRoles();
        $user = $this->userService->getUser($uuid);
        return view('pages.rolemanagement.users._edit', compact('user', 'roles', 'genders', 'statusArr', 'masterCountry'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return Response
     */
    // public function update(UpdateUserRequest $request, User $user)
    public function update(UpdateUserRequest $request, string $uuid)
    {
        $this->userService->updateUser($request, $uuid);
        return redirect()->route('user.index')->with('success', trans('admin.message.updated', ['module' => trans('admin.label.user')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     */
    // public function destroy(User $user)
    public function destroy(string $uuid)
    {
        $this->userService->deleteUser($uuid);
        return redirect()->route('user.index')->with('success', trans('admin.message.deleted', ['module' => trans('admin.label.user')]));
    }

    public function multipleDelete(Request $request)
    {
        if (!empty($request->get('ids'))) {
            $this->userService->deleteMultipleUser($request);
            return redirect()->route('user.index')->with('success', trans('admin.message.deleted', ['module' => trans('admin.label.user')]));
        }
        return redirect()->route('user.index')->with('success', trans('admin.message.select_record'));
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
                $actions = View::make('pages.rolemanagement.users._action-menu', ['data' => $data])->render();
                return $actions;
            })
            ->addColumn('check', function ($data) {
                return '<div class="form-check form-check-custom form-check-sm"><input class="form-check-input" type="checkbox" id="checkbox"' . $data->id . '" name="row_id" value="' . $data->id . '"></div>';
            })
            ->addColumn('name', function ($data) {
                return '<a href="' . route("user.edit", $data->uuid) . '">' . $data->first_name . ' ' . $data->last_name . '</a>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->whereRaw('CONCAT(first_name,last_name) like ?', ["%{$keyword}%"]);
            })
            ->editColumn('email', function ($data) {
                return $data->email;
            })
            ->editColumn('role.name', function ($data) {
                return isset($data->role->name) ? $data->role->name : "";
            })
            ->editColumn('gender', function ($data) {
                return $data->gender;
            })
            ->editColumn('status', function ($data) {
                return $data->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">In-active</span>';
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at != null ? $data->created_at->format('d-m-Y H:i:s') : '';
            })

            ->escapeColumns([])
            ->make(true);

    }
}