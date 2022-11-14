<?php

namespace App\Http\Controllers\RoleManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleManagement\StoreModuleRequest;
use App\Models\RoleManagement\Module;
use App\Services\ModuleService;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class ModuleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct(ModuleService $moduleService)
    {
        $this->middleware('permission:list-module');
        $this->middleware('permission:create-module', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-module', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-module', ['only' => ['destroy']]);
        $this->moduleService = $moduleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->moduleService->getModuleData();
            return $this->initDataTable($data);
        } else {
            return view('pages.rolemanagement.modules.index');
        }

        //return $dataTable->render('pages.rolemanagement.modules.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('pages.rolemanagement.modules._add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreModuleRequest $request
     * @return Response
     */
    public function store(StoreModuleRequest $request)
    {
        try {
            $this->moduleService->storeModule($request);
            return redirect()->route('module.index')->with('success', trans('admin.message.created', ['module' => trans('admin.label.module')]));
        } catch (QueryException $ex) {
            if ($ex->errorInfo[1] == 1062) {
                return redirect()->route('module.create')->with('success', trans('admin.message.unique', ['field' => trans('admin.label.name')]));
            } else {
                return redirect()->route('module.create')->with('error', trans('admin.message.error'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Module $module
     * @return Response
     */
    public function show(Module $module)
    {
        return view('pages.rolemanagement.modules._edit', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Module $data
     * @return Response
     */
    // public function edit(Module $module)
    public function edit(string $uuid)
    {
        $module = $this->moduleService->getModule($uuid);
        return view('pages.rolemanagement.modules._edit', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreModuleRequest $request
     * @param Module $module
     * @return Response
     */
    public function update(StoreModuleRequest $request, Module $module)
    {
        $this->moduleService->updateModule($request, $module);
        return redirect()->route('module.index')->with('success', trans('admin.message.updated', ['module' => trans('admin.label.module')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Module $module
     * @return Response
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('module.index')->with('success', trans('admin.message.deleted', ['module' => trans('admin.label.module')]));
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
                return view('pages.rolemanagement.modules._action-menu', compact('data'));
            })
            ->editColumn('name', function ($data) {
                return '<a href="' . route('module.edit', $data->uuid) . '">' . $data->name . '</a>';
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('m-d-Y H:i:s');
            })
            ->escapeColumns([])
            ->make(true);
    }
}
