<?php

namespace App\Services;

use App\Models\OrderPicklist;
use App\Models\PicklistItem;
use Carbon\Carbon;
// use Dompdf\Dompdf;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class OrderPicklistService
{
    public $status_colors_span = [1 => 'bg-danger', 2 => 'bg-warning', 3 => 'bg-success'];

    public function getOrderPicklistManagementData(Request $request)
    {
        $data = OrderPicklist::with('warehouseStaff')->orderby('created_at', 'DESC')
            ->when(request('picklist_date_range_filter'), function ($query) {
                $dateRange = explode('-', str_replace(" ", "", request('picklist_date_range_filter')));
                $startDate = Carbon::createFromFormat('d/m/Y', $dateRange[0])->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $dateRange[1])->format('Y-m-d');
                if ($startDate == $endDate) {
                    $query->whereDate('created_at', $startDate);
                } else {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            })->when(request('picklist_status_filter'), function ($query) {
                $query->where('status', request('picklist_status_filter'));
            })->when(request('packing_station_id'), function ($query) {
                $query->where('packing_station_id', request('packing_station_id'));
            });

        return $data;
    }
    public function orderPicklistStore(Request $request, object $orderData, object $itemDetails)
    {
        //get total quantity and items
        $totalQuantity = $totalItems = 0;

        foreach ($orderData as $data) {
            $totalQuantity += $data->total_qty;
            $totalItems += $data->total_items;
        }
        // save picklist
        $createOrderPickList = OrderPicklist::create([
            'packing_station_id' => $request->packing_station_id,
            'total_items' => $totalItems,
            'total_qty' => $totalQuantity,
            'status' => Config::get('params.picklist_status_key.pending'),
        ]);
        // save picklist items
        $PickOrderDetail = [];
        foreach ($itemDetails as $data) {
            $picklistItem = new PicklistItem();
            $picklistItem->master_product_id = $data->master_product_id;
            $picklistItem->qty = $data->qty;
            $picklistItem->master_order_item_id = $data->id;
            $picklistItem->location = 4;
            $picklistItem->status = Config::get('params.picklist_status_key.pending');
            $PickOrderDetail[] = $picklistItem;
        }
        $createOrderPickList->getPicklistItem()->saveMany($PickOrderDetail);
    }

    public function updateAssignee(array $request)
    {
        return OrderPicklist::where('uuid', $request['id'])->firstOrFail()->update(['staff_id' => $request['staff_id']]);
    }

    public function getSinglePicklistWithItems(string $uuid)
    {
        $data = OrderPicklist::with(['warehouseStaff', 'getPicklistItem' => function ($query) {
            $query->with('product:id,title,total_stock,item_code_sku')->select('id', 'uuid', 'order_picklist_id', 'master_product_id', 'status', DB::raw('SUM(qty) as total_qty'))->groupBy('master_product_id');
        }])->where('uuid', $uuid)->firstOrFail();
        return $data;
    }

    public function picklistStatusInProgress(string $uuid)
    {
        $pickList = OrderPicklist::where(['uuid' => $uuid, 'status' => Config::get('params.picklist_status_key.pending')])->firstOrFail();
        if (!empty($pickList)) {
            $pickList->update(['status' => Config::get('params.picklist_status_key.inprogress')]);
            $pickList->getPicklistItem()->update(['status' => Config::get('params.picklist_status_key.inprogress')]);
        }
    }
    public function picklistStatusCompleted(string $uuid)
    {
        $pickList = OrderPicklist::where(['uuid' => $uuid, 'status' => Config::get('params.picklist_status_key.inprogress')])->firstOrFail();
        if (!empty($pickList)) {
            $pickList->update(['status' => Config::get('params.picklist_status_key.completed')]);
            $pickList->getPicklistItem()->where('status', Config::get('params.picklist_status_key.inprogress'))->update(['status' => Config::get('params.picklist_status_key.completed')]);
            $pickListItems = $pickList->getPicklistItem->where('status', Config::get('params.picklist_status_key.completed'));
            foreach ($pickListItems as $data) {
                $update_status = Config::get('params.order_status_key.picked');
                //get order item
                $order_item = $data->masterOrderItems()->where('status', Config::get('params.order_status_key.inpicking'))->first();
                addOrderTrackStatus($order_item, $update_status, 1);
                $data->masterOrderItems()->where('status', Config::get('params.order_status_key.inpicking'))->update(['status' => $update_status]);
            }
        }
    }
    public function deletePicklist(string $uuid)
    {
        $pickList = OrderPicklist::where(['uuid' => $uuid, 'status' => Config::get('params.picklist_status_key.pending')])->firstOrFail();
        if (!empty($pickList)) {
            $pickListItems = $pickList->getPicklistItem->where('status', Config::get('params.picklist_status_key.pending'));
            foreach ($pickListItems as $data) {
                $data->masterOrderItems()->where('status', Config::get('params.order_status_key.inpicking'))->update(['status' => Config::get('params.order_status_key.unshipped')]);
            }
            $pickList->getPicklistItem()->delete();
            $pickList->delete();
        }
    }

    public function destroyPicklistItem(Request $request)
    {
        $picklistItems = PicklistItem::with('masterOrderItems')->where(['order_picklist_id' => $request->order_picklist_id, 'master_product_id' => $request->master_product_id]);
        if (!empty($picklistItems)) {
            foreach ($picklistItems->get() as $data) {
                $orderItemData = $data->masterOrderItems()->where('status', Config::get('params.order_status_key.inpicking'))->update(['status' => Config::get('params.order_status_key.unshipped')]);
                addOrderTrackStatus($data->masterOrderItems, Config::get('params.order_status_key.unshipped'), 1);
            }
            $picklistItems->delete();
            $this->updatePicklistCounts($request->order_picklist_id);
        }
    }

    public function updatePicklistCounts(int $id)
    {
        $orderPicklistData = OrderPicklist::find($id);
        if (!empty($orderPicklistData)) {
            $data = $this->getSinglePicklistWithItems($orderPicklistData->uuid);
            $totalQuantity = 0;
            foreach ($data->getPicklistItem as $dataorder) {
                $totalQuantity += $dataorder->total_qty;
            }
            $orderPicklistData->total_items = $data->getPicklistItem->count();
            $orderPicklistData->total_qty = $totalQuantity;
            $orderPicklistData->save();
        }
    }
    public function printFbcPicklist(string $uuid)
    {
        $badgeColor = $this->status_colors_span;
        $data = $this->getSinglePicklistWithItems($uuid);
        // $document = new Dompdf(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        // $pdfHtml = view('pages.fbc-warehouse.fbc-picklist.print-order-picklist', compact('data', 'badgeColor'));
        // $document->loadHtml($pdfHtml);
        // $document->setPaper('A4', 'landscape');
        // $document->render();
        // return $document->stream($data->uuid . '.pdf');

        $pdf = PDF::loadView('pages.fbc-warehouse.fbc-picklist.print-order-picklist', compact('data', 'badgeColor'))
            ->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true])
            ->setPaper('A4', 'portrait');

        return $pdf->download($data->uuid . '.pdf');
    }
}
