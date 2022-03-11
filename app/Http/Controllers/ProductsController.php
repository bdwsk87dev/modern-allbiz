<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProductsController extends Controller
{
    public function getPoorProductsAdw(){

        // Get all accounts from current user
        $Accounts = Account::with('user')->get();

        // Foreach all accounts
        foreach($Accounts as $account){
            // Get account settings Tokens etc...
            $settings = $account->getDecryptAll();
            // Create adwords api controller
            $AdwordsApiController = new AdwordsApiController($settings);

            // Get all clients from api by settings


            $AdwordsApiController->setClientCustomerId('8287870080');
            $AdwordsApiController->init();
            $res = $AdwordsApiController->getPoorProducts();
            dd($res);
            return $result;
        }
    }
}
