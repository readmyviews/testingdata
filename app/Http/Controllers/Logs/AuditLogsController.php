<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Activitylog\Models\Activity;

class AuditLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request, AuditLogService $auditLogService)
    {
        if ($request->ajax()) {
            $data = $auditLogService->getActivityData();

            return $this->initDataTable($data);
        }
        return view('pages.log.audit.index');
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
            ->editColumn('id', function (Activity $model) {
                return $model->id;
            })
            ->editColumn('subject_id', function (Activity $model) {
                return $model->subject_id;
            })
            ->editColumn('causer_id', function (Activity $model) {
                return $model->causer ? $model->causer->first_name : __('System');
            })
            ->editColumn('properties', function (Activity $model) {
                $content = $model->properties;

                return view('pages.log.audit._details', compact('content'));
            })
            ->editColumn('created_at', function (Activity $model) {
                return $model->created_at->format('d M, Y H:i:s');
            })
            ->escapeColumns([])
            ->make(true);

    }
}