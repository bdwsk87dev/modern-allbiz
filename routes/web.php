<?php
use App\Http\Controllers\CheckerController;
use App\Models\Account;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\TaskLogController;
use App\Http\Controllers\DailyTasksLogController;
use App\Http\Controllers\CampaignsCheckerController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsApiController;

// Main page
Route::get('/', function () {
    return redirect('/login');
});

// Other pages
Route::middleware(['auth:sanctum', 'verified'])->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/admin/dashboard', function () {
        return Inertia\Inertia::render('Dashboard');
    })->name('dashboard');
    // Accounts
    Route::get('/admin/accounts', function () {
        return Inertia\Inertia::render('Accounts');
    })->name('accounts');
    // Customers
    Route::get('/admin/customers', function () {
        return Inertia\Inertia::render('Customers');
    })->name('customers');
    // Campaigns
    Route::get('/admin/campaigns', function () {
        return Inertia\Inertia::render('Campaigns');
    })->name('campaigns');
    // Checker history
    Route::get('/admin/checker-history', function () {
        return Inertia\Inertia::render('CheckerHistory');
    })->name('checker_history');
    // History
    Route::get('/admin/daily_checker-history', function () {
        return Inertia\Inertia::render('DailyCheckerHistory');
    })->name('daily_checker_history');
    // New campaign history
    Route::get('/admin/campaigns_history', function () {
        return Inertia\Inertia::render('CampaignsHistory');
    })->name('campaigns_history');
    // New customers history
    Route::get('/admin/customers_history', function () {
        return Inertia\Inertia::render('CustomersHistory');
    })->name('customers_history');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // adwords
    Route::post('/api/v1/campaigns/adw/get', [CampaignsController::class, 'getFromAdwordsApi']);
    // Working with accounts
    Route::post('/api/v1/accounts/list', [AccountsController::class, 'list']);
    Route::post('/api/v1/accounts/simple_list', [AccountsController::class, 'simpleList']);
    Route::post('/api/v1/accounts/update/{account_id}', [AccountsController::class, 'update']);
    Route::post('/api/v1/accounts/delete/{account_id}', [AccountsController::class, 'delete']);
    // Working with customers
    Route::post('/api/v1/customers', [CustomersController::class, 'getData']);
    // Campaigns
    // Get campaigns list
    Route::post('/api/v1/campaigns/list', [CampaignsController::class, 'list']);
    // Delete campaign
    Route::post('/api/v1/campaign/delete/{campaign_id}', [CampaignsController::class, 'delete']);
    // Get suitable pairs for campaigns
    Route::get('/api/v1/campaign/getsuitablepairs/{campaign_id}', [CampaignsController::class, 'getsuitablepairs']);
    Route::post('/api/v1/campaign/setpair/{campaign_id}', [CampaignsController::class, 'setPairCampaign']);
    Route::post('/api/v1/campaign/savestatus/{campaign_id}', [CampaignsController::class, 'changeStatus']);
    Route::post('/api/v1/campaign/update/{campaign_id}', [CampaignsController::class, 'update']);
    Route::get('/api/v1/campaign/adw/get/status/{campaign_id}', [CampaignsController::class, 'adwGetCampaign']);
    Route::post('/api/v1/campaign/adw/update/status/{campaign_id}', [CampaignsController::class, 'adwUpdateCampaignStatus']);
    // Route::put('/api/v1/accounts/update/{account_id}', [AccountsController::class, 'update']);
    Route::post('/api/v1/accounts/create', [AccountsController::class, 'store']);
    Route::post('/api/v1/accounts/generate-refresh-token ', [AccountsController::class, 'generateRefreshToken']);
    // Working with logs
    Route::post('/api/v1/task-log/list', [TaskLogController::class, 'list']);
    Route::post('/api/v1/daily-task-log/list', [DailyTasksLogController::class, 'list']);
    // Страница история кампаний.
    // Campaigns history
    Route::post('/api/v1/history/campaigns/list', [HistoryController::class, 'getCampaignsList']);
    // Страница история кампаний. Запрос на данные по одному клиенту
    Route::get('/api/v1/history/campaign/{campaign_id}/{date}', [HistoryController::class, 'getCampaign']); // ??
    Route::post('/api/v1/history/customers/list', [HistoryController::class, 'getCustomersList']);
    // Страница история клиентов. Запрос на данные по одному клиенту
    Route::get('/api/v1/history/customer/{customer_id}/{date}', [HistoryController::class, 'getCustomer']); // ??
});

/*
 * Global checker
 */
Route::get('/checker', [CheckerController::class, 'start']);
Route::get('/campprefupdate', [CampaignsController::class, 'campprefupdate']);
Route::get('/daily_checker', function () {
    //$campaignChecker = new CampaignsCheckerController();
    //$campaignChecker->daily_checker_start();
});

Route::get('/clear', function() { Artisan::call('cache:clear'); Artisan::call('config:clear'); Artisan::call('config:cache'); Artisan::call('view:clear'); Artisan::call('route:clear'); return "Cleared!"; });
Route::post('/api/auth/login', [AuthController::class, 'login']);

// Oath
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'App\Http\Controllers\AuthController@logout');
    });
});

/*
 * Workin with Google Adwords products
 */
Route::middleware(['auth:sanctum', 'verified'])->name('admin.')->group(function () {
    Route::get('/adwords-product-manager/list', function () {
        return Inertia\Inertia::render('AdwPMList');
    })->name('adwords-product-manager-list');
});










