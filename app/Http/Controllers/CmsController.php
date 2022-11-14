<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCmsRequest;
use App\Models\Cms;
use App\Services\CmsService;
use Config;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class CmsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct(CmsService $cmsService)
    {
        $this->cmsService = $cmsService;
        $this->middleware('permission:list-cms');
        $this->middleware('permission:create-cms', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-cms', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-cms', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->cmsService->getCmsList($request);
            return $this->initDataTable($data);
        } else {
            return view('pages.cms.index');
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
        return view('pages.cms._add', compact('statusArr'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCmsRequest $request
     * @return Response
     */
    public function store(StoreCmsRequest $request)
    {
        $this->cmsService->storeCms($request);
        return redirect()->route('cms.index')->with('success', trans('admin.message.created', ['module' => trans('admin.label.cms')]));
    }

    /**
     * Display the specified resource.
     *
     * @param Cms $cmsPage
     * @return Response
     */
    public function show(Cms $cms)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($uuid)
    {
        $data = $this->cmsService->getCms($uuid);
        $statusArr = Config::get('params.status');
        return view('pages.cms._edit', compact('data', 'statusArr'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreCmsRequest $request
     * @param int $id
     * @return Response
     */
    public function update(StoreCmsRequest $request, string $uuid)
    {
        $request->merge(['uuid' => $uuid]);
        $this->cmsService->updateCms($request);
        return redirect()->route('cms.index')->with('success', trans('admin.message.updated', ['module' => trans('admin.label.cms')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(string $uuid)
    {
        $this->cmsService->deleteCms($uuid);
        return redirect()->route('cms.index')->with('success', trans('admin.message.deleted', ['module' => trans('admin.label.cms')]));
    }

    /**
     * Remove the multiple resources from storage.
     *
     * @param Request $request
     * @return Response
     */
    public function multipleDelete(Request $request)
    {
        if (!empty($request->get('ids'))) {
            $this->cmsService->multipleDeleteCms($request);
            return redirect()->route('cms.index')->with('success', trans('admin.message.deleted', ['module' => trans('admin.label.cms')]));
        }
        return redirect()->route('cms.index')->with('success', trans('admin.message.select_record'));
    }
    /**
     * initDataTable function
     *
     * @param [type] $data
     * @return object
     */
    public function initDataTable($data)
    {
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return view('pages.cms._action-menu', compact('data'));
            })
            ->addColumn('check', function ($data) {
                return '<div class="form-check form-check-custom form-check-sm"><input class="form-check-input" type="checkbox" id="checkbox"' . $data->uuid . '" name="row_id" value="' . $data->uuid . '"></div>';
            })
            ->editColumn('title', function ($data) {
                return '<a href="' . route("cms.edit", $data->uuid) . '">' . $data->title . '</a>';
            })
            ->editColumn('slug', function ($data) {
                return $data->slug;
            })
            ->editColumn('status', function ($data) {
                return $data->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">In-active</span>';
            })
            ->editColumn('is_mobile_view', function ($data) {
                return $data->is_mobile_view == 1 ? '<span class=" badge badge-success ">Yes</span>' : '<span class=" badge badge-warning ">No</span>';
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('d/m/Y H:i:s');
            })
            ->escapeColumns([])
            ->make(true);
    }
}
