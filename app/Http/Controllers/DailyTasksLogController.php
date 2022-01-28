<?php

namespace App\Http\Controllers;

use App\Models\DailyTaskLogs;
use Illuminate\Http\Request;

class DailyTasksLogController extends Controller
{
    public function store($data){
        $log = new DailyTaskLogs();
        $log->type = (isset($data['type']) && $data['type'] != '') ? $data['type'] : '';
        $log->customer_id = (isset($data['customer_id']) && $data['customer_id'] != '') ? $data['customer_id'] : '';
        $log->campaign_id = (isset($data['campaign_id']) && $data['campaign_id'] != '') ? $data['campaign_id'] : '';
        $log->message = (isset($data['message']) && $data['message'] != '') ? $data['message'] : '';
        $log->task_id = (isset($data['task_id']) && $data['task_id'] != '') ? $data['task_id'] : '';
        $log->save();
    }

    public function list(Request $request)
    {
        // Получение списка логов из базы данных приложения
        // Получаем поле по которому необходимо выполнить сортировку
        $sortField = $request->get('sortField');
        // Получаем значение строки поиска
        $searchString = $request->input('searchString');
        // Получаем порядок сортировки
        $order = ($request->input('type')) ? 'desc' : 'asc';
        // Получаем записи
        $query = DailyTaskLogs::
        where('type', 'LIKE', '%' . $searchString . '%')
            ->orWhere('customer_id', 'LIKE', '%' . $searchString . '%')
            ->orWhere('campaign_id', 'LIKE', '%' . $searchString . '%')
            ->orWhere('message', 'LIKE', '%' . $searchString . '%')
            ->orWhere('created_at', 'message', '%' . $searchString . '%')
            ->orderBy($sortField, $order)
            ->paginate(35);
        return $query;
    }
}
