<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Http\Traits\Helper;
use Illuminate\Support\Facades\Artisan;
use Edujugon\GoogleAds\GoogleAds;

class AccountsController extends Controller
{
    use Helper;

    /**
     * @param Request $request
     * @return mixed
     */
    public function list(Request $request)
    {
        /**
         * Получаем поле по которому необходимо выполнить сортировку,
         * значение строки поиска и Получаем порядок сортировки
         */
        $sortField = $request->get('sortField');
        $searchString = $request->input('searchString');
        $order = ($request->input('type')) ? 'desc' : 'asc';
        /*
         * Получаем список аккаунтов
         */
        $accounts = Account::where('id', 'LIKE', '%' . $searchString . '%')
            ->orWhere('account_name', 'like', '%' . $searchString . '%')
            ->orWhere('description', 'LIKE', '%' . $searchString . '%')
            ->orWhere('email', 'LIKE', '%' . $searchString . '%')
            ->orderBy($sortField, $order)
            ->paginate(15);
        /*
         * Расшифровка зашифрованных данных.
         */
        foreach ($accounts->fragment('items') as &$item) {
            $item['client_id'] = ($item['client_id']=="") ? '' : $item->getCryptAttribute('client_id');
            $item['developer_token'] = ($item['developer_token']=="") ? '' : $item->getCryptAttribute('developer_token');
            $item['client_secret'] = ($item['client_secret']=="") ? '' : $item->getCryptAttribute('client_secret');
            $item['refresh_token'] = ($item['refresh_token']=="") ? '' : $item->getCryptAttribute('refresh_token');
        }
        /*
         * Возвращаем результат
         */
        return $accounts;
    }

    public function generateRefreshToken(Request $request){
        $data = $request->input('data');
        $settings = include (base_path().'/config/google-ads.php');
        $oAuth2Settings = $settings['oAuth2'];
        $url = $oAuth2Settings['authorizationUri'].'?response_type=code&access_type=offline&client_id='.$data['client_id'].'&redirect_uri='.urlencode($oAuth2Settings['redirectUri']).'&scope='.urlencode($oAuth2Settings['scope']);
        return $url;
    }

    /**
     * @return mixed
     */
    public function simpleList(){
        $accounts = Account::select('id', 'account_name')->get();
        return $accounts;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        /*
         * Создаём инстанс обьекта аккаунт
         */
        $account = new Account();
        /*
         * Получаем данные с запроса
         */
        $data = $request->input('data');
        /*
         * Проверка данных на пустоту
         */
        if (!isset($data) || empty($data)) {
            return response()->json(['status' => false, 'message' => __('accounts.create_empty_request')]);
        }
        /*
         * Работа с полями
         */
        if (isset($data['account_name'])) {
            $account->account_name = $data['account_name'];
        }
        if (isset($data['description'])) {
            $account->description = $data['description'];
        }
        if (isset($data['client_id'])) {
            $account->setCryptAttribute('client_id', $data['client_id']);
        }
        if (isset($data['developer_token'])) {
            $account->setCryptAttribute('developer_token', $data['developer_token']);
        }
        if (isset($data['client_secret'])) {
            $account->setCryptAttribute('client_secret', $data['client_secret']);
        }
        if (isset($data['refresh_token'])) {
            $account->setCryptAttribute('refresh_token', $data['refresh_token']);
        }
        if (isset($data['email'])) {
            $account->email = $data['email'];
        }
        /*
         * Создаём аккаунт.
         */
        if (!$account->save()) {
            return response()->json(['status' => false, 'message' => __('accounts.create_failed')]);
        }
        /*
         * Возвращаем статус удачного завершения создания аккаунта
         */
        return response()->json(['status' => true, 'message' => __('accounts.create_success')]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        /*
         * Получаем данные с запроса
         */
        $data = $request->input('account_data');
        /*
         * Проверка данных на пустоту
         */
        if (!isset($data) || empty($data)) {
            return response()->json(['status' => false, 'message' => __('accounts.create_empty_request')]);
        }
        /*
         * Получаем аккаунт, который необходимо обновить
         */
        $account = Account::where('id', '=', $id)->first();
        /*
         * Проверяем аккаунт на существование
         */
        if (empty($account)) {
            return response()->json(['status' => false, 'message' => __('accounts.update_failed')]);
        }
        /*
         * Работа с полями
         */
        if (isset($data['account_name'])) {
            $account->account_name = $data['account_name'];
        }
        if (isset($data['active'])) {
            $account->active = $data['active'];
        }
        if (isset($data['testing'])) {
            $account->testing = $data['testing'];
        }
        if (isset($data['seven_days'])) {
            $account->seven_days = $data['seven_days'];
        }
        if (isset($data['description'])) {
            $account->description = $data['description'];
        }
        if (isset($data['client_id'])) {
            $account->client_id = $this->encryptString($data['client_id']);
        }
        if (isset($data['developer_token'])) {
            $account->developer_token = $this->encryptString($data['developer_token']);
        }
        if (isset($data['client_secret'])) {
            $account->client_secret = $this->encryptString($data['client_secret']);
        }
        if (isset($data['refresh_token'])) {
            $account->refresh_token = $this->encryptString($data['refresh_token']);
        }
        if (isset($data['email'])) {
            $account->email = $data['email'];
        }
        /*
         * Обновляем аккаунт.
         */
        if (!$account->save()) {
            return response()->json(['status' => false, 'message' => __('accounts.update_failed')]);
        }
        /*
         * Возвращаем статус удачного завершения обновления аккаунта
         */
        return response()->json(['status' => true, 'message' => __('accounts.update_success')]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        /*
         * Получаем аккаунт, который необходимо удалить
         */
        $account = Account::where('id', '=', $id)->first();
        /*
         * Проверяем, существует ли заданый аккаунт
         */
        if (!$account) {
            return response()->json([
                'status' => false,
                'message' => __('accounts.delete_failed_no_account')
            ]);
        }
        /*
         * Удаляем аккаунт
         */
        if (!$account->delete()) {
            response()->json(['status' => false, 'message' => __('accounts.delete_failed')]);
        }
        /*
         * Возвращаем статус удачного удаления аккаунта
         */
        return response()->json(['status' => true, 'message' => __('accounts.delete_success')]);
    }
}
