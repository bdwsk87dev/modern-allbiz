<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class ProductsApiController extends Controller
{
    public function index(){
        // Получаем все аккаунты
        $Accounts = Account::all();
        // Перебераем аккаунты
        foreach($Accounts as $account){
            $settings = $account->getDecryptAll();
            $AdwordsApiController = new AdwordsApiController($settings);

                dd($AdwordsApiController);
        }
    }
}
