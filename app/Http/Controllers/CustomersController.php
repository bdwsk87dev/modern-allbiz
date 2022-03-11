<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Campaign;
use App\Models\Customer;
use Illuminate\Http\Request;

// TODO
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
    public function getData(Request $request)
    {
        switch ($request->input('requestType')) {
            case 'update':
                $customer = Customer::where('id','=',$request->input('accountId'))->first();
                $customer->start_budget = $request->input('start_budget');
                $customer->max_budget = $request->input('max_budget');
                $customer->save();
                return response()->json(['status' => true, 'message' => 'Клиент был сохранён']);
                break;
            case 'getCustomersForSelect':
                // Получение списка customers только для выбора во время импорта
                $customersFilter = $request->input('customersFilter');
                $accounts = Customer::select('id', 'customer_name', 'customer_id')->where('customer_name', 'like', '%'.$customersFilter.'%')->orderBy('created_at', 'desc')->take(100)->get();
                return $accounts;
                break;
            case 'getCustomers':
                // Получение списка Customers из базы данных приложения
                // Получаем поле по которому необходимо выполнить сортировку
                $sortField = $request->input('sortField');
                // Получаем значение строки поиска
                $searchString = $request->input('searchString');
                // Обращаемся к базе данных
                $order = ($request->input('type')) ? 'desc' : 'asc';
                $itemsPerPage = 15;
                if($request->has('itemsPerPage')){
                    $itemsPerPage = $request->input('itemsPerPage');
                }

                // Сам запрос
                $query = Customer::leftjoin('accounts', 'customers.account_id', '=', 'accounts.id')
                    ->select(
                        'customers.id as idCustomer',
                        'can_manage_clients',
                        'customers.customer_name', 'accounts.account_name', 'customers.account_id',
                        'customers.customer_id', 'customers.active',
                        'customers.start_budget', 'customers.max_budget', 'last_check_date'
                    )
                    ->where('customers.id', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('customers.customer_name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('accounts.account_name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('customers.account_id', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('customers.customer_id', 'LIKE', '%' . $searchString . '%')
                    ->orderBy($sortField, $order)
                    ->paginate($itemsPerPage);
                return $query;
                break;
            case 'delete':
                // Удаление customer
                // Получаю customer по id
                $customer = Customer::where('id', '=', $request->input('id'))->first();
                if (!$customer) {
                    // Если такой customer не существует, отправляем ошибку
                    return response()->json([
                        'status' => false,
                        'message' => 'Не могу удалить клиента. Такой клиент не существует'
                    ]);
                }
                // Если такой customer существуе, тогда удаляем его.
                $customer->delete();
                // Возвращаем ответ что всё нормально
                return response()->json(['status' => true, 'message' => 'Customer has ben removed!']);
                break;
            case 'import-customers-from-api':
                $newCount = 0;
                $issetCount = 0;

                // Получаем список запрашиваемых аккаунтов
                $accountId = $request->input('account_id');
                // Получаем список аккаунтов из базы данных

                $account = Account::where('id', $accountId)->first()->getDecryptAll();

                if (empty($account)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Не удалось получить аккаунт'
                    ]);
                }

                // Перебераем список аккаунтов из базы данных и делаем запрос в api

                try {
                    $AdwordsApiController = new AdwordsApiController($account);
                    $AdwordsApiController->setClientCustomerId($request->input('customer_id'));
                    $customers = $AdwordsApiController->getCustomers()->getEntries();



                } catch (\Throwable $e) {
                    return response()->json(['status' => false, 'message' => $e->getMessage()]);
                }

                if (is_array($customers) and !empty($customers)) {
                    foreach ($customers as $customer) {
                        // Проверяем, существует ли уже такой customer под указанным аккаунтом
                        $chCustomer = Customer::where('account_id','=',$account['id'])->
                            where('customer_id','=',$customer->getCustomerId())->get();
                        if($chCustomer->isEmpty()){
                            // Если такой customer не существует, создаём нужный customer и увеличиваем счётчик новых customers
                            $newCustomer = new Customer();
                            $newCustomer->customer_name = $customer->getName();
                            $newCustomer->account_id = $account['id'];
                            $newCustomer->can_manage_clients = $customer->getCanManageClients();
                            $newCustomer->customer_id = $customer->getCustomerId();
                            $newCustomer->save();
                            $newCount = ++$newCount;
                        }
                        else{
                            // Если такой customer уже существует, увеличиваем счётчик существующих customers
                            $issetCount = ++$issetCount;
                        }
                    }
                } else {
                    return response()->json(['status' => false, 'message' => 'Не удалось получить список customers. Account id'.$account['id'], 'newCustomers' => $newCount,'issetCustomers' => $issetCount ]);
                }

                return response()->json(['status' => true, 'message' => '', 'newCustomers' => $newCount,'issetCustomers' => $issetCount ]);
                break;

            case 'changeActive':
                $customer = Customer::where('id', '=', $request->input('id'))->first();
                if (!$customer) {
                    return response()->json(['status' => false, 'message' => 'Такой клиент не найден']);
                }
                $customer->active = $request->input('checked');
                $customer->save();
                return response()->json(['status' => true, 'message' => 'Action changed!']);

        }
    }
}
