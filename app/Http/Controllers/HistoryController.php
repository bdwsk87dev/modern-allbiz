<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\TaskLog;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController
{

    protected $sortField;
    protected $order;
    protected $itemId;
    protected $searchString;

    public function __construct(Request $request)
    {
        // Получаем поле по которому необходимо выполнить сортировку
        $this->sortField = $request->get('sortField');
        // Получаем порядок сортировки
        $this->order = ($request->input('type')) ? 'desc' : 'asc';
        // Строка поиска
        $this->searchString = $request->input('searchString');
    }

    public function getCustomer($customerId, $date){
        $query = DB::table('task_logs')
            ->select(
                DB::raw("DATE_FORMAT(task_logs.updated_at, '%H:%i:%s') as time"),
                'id',
                'task_id',
                'message',
                'type',
                'task',
                'campaign_id',
                'created_at')
            ->Where('customer_id', '=', $customerId)
            ->Where( DB::raw("DATE_FORMAT(task_logs.updated_at, '%d.%m.%Y')"), '=',$date)
            ->orderBy('id', 'desc')->limit(300)->get();
        return response()->json($query);
    }

    public function getCampaign($campaign_id, $date){
        $query = DB::table('task_logs')
            ->select(
                DB::raw("DATE_FORMAT(task_logs.updated_at, '%H:%i:%s') as time"),
                'id',
                'task_id',
                'message',
                'type',
                'task',
                'campaign_id',
                'created_at')
            ->Where('campaign_id', '=', $campaign_id)
            ->Where( DB::raw("DATE_FORMAT(task_logs.updated_at, '%d.%m.%Y')"), '=',$date)
            ->orderBy('id', 'desc')->limit(300)->get();
        return response()->json($query);
    }












//    public function getCampaign($campaignId, $date)
//    {
//        $query = DB::table('task_logs')
//            ->select(
//                DB::raw("DATE_FORMAT(task_logs.updated_at, '%d.%m.%Y') as date"),
//                'task_id',
//                'message',
//                'type',
//                'created_at')
//            ->Where('campaign_id', '=', $campaignId)
//            ->orWhere('updated_at', '=', DB::raw("DATE_FORMAT(task_logs.updated_at, '%d.%m.%Y')"))
//            ->orderBy('updated_at', 'asc')->limit(100)->get();
//        return response()->json($query);
//    }

    public function getCampaignsList(Request $request)
    {
        $query = TaskLog::leftjoin('customers', 'task_logs.customer_id', '=', 'customers.customer_id')
            ->select(
                'task_logs.id',
                'task_logs.customer_id',
                'task_logs.type',
                'task_logs.campaign_id',
                'task_logs.task_id',
                'customers.customer_name',
                DB::raw('count(DISTINCT task_id) as check_count'),
                DB::raw("DATE_FORMAT(task_logs.updated_at, '%d.%m.%Y') as date"),
                DB::raw('count(*) as total'),
                DB::raw('count(IF(task_logs.type = "warning", 1, NULL)) as warnings'),
                DB::raw('count(IF(task_logs.type = "error", 1, NULL)) as errors')
            )
            ->where('task_logs.campaign_id', '<>', '');
        if ($this->searchString != '') {
            $query = $query->where('customers.customer_name', 'LIKE', '%' . $this->searchString . '%')
                ->orWhere('task_logs.customer_id', 'LIKE', '%' . $this->searchString . '%')
                ->orWhere('task_logs.updated_at', 'LIKE', '%' . $this->searchString . '%');
        }
        $query = $query
            ->groupBy('task_logs.task_id')
            ->groupBy('task_logs.campaign_id')
            ->groupBy('date');

        if ($this->sortField == "date") {
            $query = $query->orderBy(DB::raw("DATE_FORMAT(updated_at, '%Y.%m.%d')"), $this->order);
        } else {
            $query = $query->orderBy($this->sortField, $this->order);
            $query = $query->orderBy("task_logs.updated_at", $this->order);
        }
        $query = $query->paginate(25);
        return response()->json($query);
    }

    public function getCustomersList(Request $request)
    {
        $query = TaskLog::leftjoin('customers', 'task_logs.customer_id', '=', 'customers.customer_id')
            ->select(
                'task_logs.id',
                'task_logs.customer_id',
                'task_logs.type',
                'task_logs.campaign_id',
                'task_logs.task_id',
                'customers.customer_name',
                DB::raw('count(DISTINCT task_id) as check_count'),
                DB::raw("DATE_FORMAT(task_logs.updated_at, '%d.%m.%Y') as date"),
                DB::raw('count(*) as total'),
                DB::raw('count(IF(task_logs.type = "warning", 1, NULL)) as warnings'),
                DB::raw('count(IF(task_logs.type = "error", 1, NULL)) as errors')
            )
            ->where('task_logs.customer_id', '<>', '');
        if ($this->searchString != '') {
            $query = $query->where('customers.customer_name', 'LIKE', '%' . $this->searchString . '%')
                ->orWhere('task_logs.customer_id', 'LIKE', '%' . $this->searchString . '%')
                ->orWhere('task_logs.updated_at', 'LIKE', '%' . $this->searchString . '%');
        }
        $query = $query
            ->groupBy('task_logs.task_id')
            ->groupBy('task_logs.customer_id')
            ->groupBy('date');

        if ($this->sortField == "date") {
            $query = $query->orderBy(DB::raw("DATE_FORMAT(updated_at, '%Y.%m.%d')"), $this->order);
        } else {
            $query = $query->orderBy($this->sortField, $this->order);
            $query = $query->orderBy("task_logs.updated_at", $this->order);
        }
        $query = $query->paginate(25);
        return response()->json($query);
    }
}


//
//public function getCustomersList_old(Request $request)
//{
//    // Для истории
//    $query = TaskLog::with('customer')->
//    select(DB::raw('count(DISTINCT task_id) as check_count'), 'type', 'customer_id', 'campaign_id', 'task_id', DB::raw("DATE_FORMAT(updated_at, '%d.%m.%Y') as date"), DB::raw('count(*) as total'),
//        DB::raw('count(IF(type = "warning", 1, NULL)) as warnings'),
//        DB::raw('count(IF(type = "error", 1, NULL)) as errors'))
//        ->where('customer_id', '<>', '')
//        ->groupBy('task_id')
//        ->groupBy('customer_id')
//        ->groupBy('date');
//    if ($this->sortField == "date") {
//        $query = $query->orderBy(DB::raw("DATE_FORMAT(updated_at, '%Y.%m.%d')"), $this->order);
//    } else {
//        $query = $query->orderBy($this->sortField, $this->order);
//    }
//    $query = $query->paginate(25);
//    return response()->json($query);
//}
//
//public function getCampaignsList_old(Request $request)
//{
//    $query = TaskLog::with('customer')->
//    select(DB::raw('count(DISTINCT task_id) as check_count'), 'type', 'customer_id', 'campaign_id', 'task_id', DB::raw("DATE_FORMAT(updated_at, '%d.%m.%Y') as date"), DB::raw('count(*) as total'),
//        DB::raw('count(IF(type = "warning", 1, NULL)) as warnings'),
//        DB::raw('count(IF(type = "error", 1, NULL)) as errors'))
//        ->whereNotNull('campaign_id')
//        ->where('campaign_id', '<>', '')
//        ->groupBy('task_id')
//        ->groupBy('campaign_id')
//        ->groupBy('date');
//
//    if ($this->sortField == "date") {
//        $query = $query->orderBy(DB::raw("DATE_FORMAT(updated_at, '%Y.%m.%d')"), $this->order);
//    } else {
//        $query = $query->orderBy($this->sortField, $this->order);
//    }
//
//    $query = $query->paginate(25);
//    return response()->json($query);
//}