<?php

namespace App\Http\Controllers\Logs;

use App\DataTables\Logs\SystemLogsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Jackiedo\LogReader\LogReader;

class SystemLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(SystemLogsDataTable $dataTable)
    {
        return $dataTable->render('pages.log.system.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id, LogReader $logReader)
    {
        return $logReader->find($id)->delete();
    }
}
