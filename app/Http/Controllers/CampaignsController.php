<?php
// helper
// getAdvertisingChannelType - search or shopping
// getAdvertisingChannelSubType - is smart or not

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Campaign;
use App\Models\Customer;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Checker;
use Google\AdsApi\AdWords\v201809\cm\CampaignCriterion;

class CampaignsController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function list(Request $request)
    {
        // Получение списка компаний из базы данных приложения
        // Получаем поле по которому необходимо выполнить сортировку
        $sortField = $request->get('sortField');
        // Получаем значение строки поиска
        $searchString = $request->input('searchString');
        // Получаем порядок сортировки
        $order = ($request->input('type')) ? 'desc' : 'asc';
        // Получаем записи
        $query = Campaign::leftjoin('customers', 'campaigns.customer_id', '=', 'customers.id')
            ->select(
                'campaigns.id as idCampaign',
                'campaigns.campaign_name',
                'campaigns.campaign_id',
                'campaigns.phase',
                'campaigns.last_check_date',
                'customers.customer_name',
                'campaigns.active',
                'campaigns.is_smart',
                'campaigns.status',
                'campaigns.link_campaign',
                'campaigns.cost',
                'campaigns.conv',
                'campaigns.conv_value',
                'campaigns.cost_30',
                'campaigns.conv_30',
                'campaigns.conv_value_30',
                'customers.customer_id'
            )
            ->where('campaigns.id', 'LIKE', '%' . $searchString . '%')
            ->orWhere('campaigns.campaign_name', 'LIKE', '%' . $searchString . '%')
            ->orWhere('campaigns.campaign_id', 'LIKE', '%' . $searchString . '%')
            ->orWhere('customers.customer_id', 'LIKE', '%' . str_replace('-','',$searchString).'%')
            ->orWhere('customers.customer_name', 'LIKE', '%' . $searchString . '%')
            ->orderBy($sortField, $order)
            ->paginate(15);
        return $query;
    }

    public function getsuitablepairs($campaignId)
    {
        $query = Campaign::where('id', '=', $campaignId)->first();
        if (empty($query)) {
            return response()->json(['status' => false, 'message' => 'no campaigns!']);
        }

        $customerId = $query['customer_id'];

        $query = Campaign::leftjoin('customers', 'campaigns.customer_id', '=', 'customers.id')
            ->select(
                'campaigns.id as idCampaign',
                'campaigns.campaign_name',
                'campaigns.campaign_id',
                'campaigns.phase',
                'campaigns.last_check_date',
                'customers.customer_name',
                'campaigns.active',
                'campaigns.is_smart',
                'campaigns.status',
                'campaigns.link_campaign'
            )
            ->where('customers.id', '=', $customerId)
            ->Where('campaigns.id', '<>', $campaignId)
            ->orderBy('campaigns.is_smart', 'asc')
            ->get();

        return response()->json(['status' => true, 'message' => 'ok', 'data' => $query]);

    }


    public function setPairCampaign($campaignId, Request $request)
    {
        $unlink = $request->input('unlink');
        if ($unlink) {
            $campaign = Campaign::where('id', '=', $campaignId)->first();
            $oldPairCampaignId = $campaign->link_campaign;
            $oldPairCampaign = Campaign::where('id', '=', $oldPairCampaignId)->first();
            if (!empty($oldPairCampaign)) {
                $oldPairCampaign->link_campaign = '';
                $oldPairCampaign->save();
            }
            $campaign->link_campaign = '';
            $campaign->save();
            return true;
        }
        $pairCampaignId = $request->input('pairCampaignId');
        try {
            $campaign = Campaign::where('id', '=', $campaignId)->first();
            if (empty($campaign)) {
                return response()->json(['status' => false, 'message' => __('campaigns.update_failed')]);
            }
            // Удаляем старую привязку
            $oldPairCampaignId = $campaign->link_campaign;
            $oldPairCampaign = Campaign::where('id', '=', $oldPairCampaignId)->first();
            if (!empty($oldPairCampaign)) {
                $oldPairCampaign->link_campaign = '';
                $oldPairCampaign->save();
            }
            // Мустанавливаем новую привязку
            $campaign->link_campaign = $pairCampaignId;
            $campaign->save();

            // Меняем привязку для привязуемой кампании
            $campaign = Campaign::where('id', '=', $pairCampaignId)->first();
            if (empty($campaign)) {
                return response()->json(['status' => false, 'message' => __('campaigns.update_failed')]);
            }
            // Удаляем старую привязку
            $oldPairCampaignId = $campaign->link_campaign;
            $oldPairCampaign = Campaign::where('id', '=', $oldPairCampaignId)->first();
            if (!empty($oldPairCampaign)) {
                $oldPairCampaign->link_campaign = '';
                $oldPairCampaign->save();
            }
            // Мустанавливаем новую привязку
            $campaign->link_campaign = $campaignId;
            $campaign->save();

        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Помилочка.' . $e]);
        }
        return response()->json(['status' => true, 'message' => 'ok']);
    }


    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $campaign_data = $request->input('campaign_data');
        $campaign = Campaign::where('id', '=', $id)->first();
        if (empty($campaign)) {
            return response()->json(['status' => false, 'message' => __('campaigns.update_failed')]);
        }
        foreach ($campaign_data as $fieldKey => $fieldValue) {
            $campaign->$fieldKey = $fieldValue;
        }
        $campaign->save();
        return response()->json(['status' => true, 'message' => __('campaigns.update_success')]);
    }

    /**
     * @param $campaignId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $campaign = Campaign::where('id', '=', $id)->first();
        if (!$campaign) {
            return response()->json([
                'status' => false,
                'message' => __('campaigns.delete_failed')
            ]);
        }
        $campaign->delete();
        return response()->json(['status' => true, 'message' => __('campaigns.delete_success')]);
    }


    public function getFromAdwordsApi(Request $request)
    {
        // Количество новых импортированных записей
        $newCount = 0;
        // Количество уже существующих записей
        $issetCount = 0;

        // Получаем список запрашиваемых клиентов
        $customers_ids = $request->input('customers_ids');

        if (is_array($customers_ids) && empty($customers_ids)) {
            return response()->json([
                'status' => false,
                'message' => __('campaigns.customer_failed')
            ]);
        }

        // Получаем список клиентов из базы данных, для дальнейшего использования их аккаунтов в Api запросах
        $customers = Customer::with('account')->whereIn('id', $customers_ids)->get();

        if (empty($customers)) {
            return response()->json([
                'status' => false,
                'message' => __('campaigns.customer_empty')
            ]);
        }

        try {
            $customers->each(function ($customer) {
                $AdwordsApiController = new AdwordsApiController($customer->getRelation('account')->getDecryptAll());
                $AdwordsApiController->setClientCustomerId($customer->customer_id);
                $campaigns = $AdwordsApiController->getShoppingCampaigns();

                // Todo поставить информацию о новых компаниях
                $campaigns->each(function ($campaign) use ($customer) {
                    $isIssetCampaign = Campaign::where('campaign_id', '=', $campaign->getId())->first();
                    if ($isIssetCampaign) {
                        return;
                    }
                    $newCampaign = new Campaign();
                    $newCampaign->campaign_name = $campaign->getName();
                    $newCampaign->campaign_id = $campaign->getId();
                    $newCampaign->customer_id = $customer->id;
                    $newCampaign->status = $campaign->getStatus();
                    $newCampaign->is_smart = ($campaign->getAdvertisingChannelSubType() == 'SHOPPING_GOAL_OPTIMIZED_ADS') ? true : false;
                    $newCampaign->phase = '0';
                    $newCampaign->active = 0;
                    $newCampaign->last_check_date = ''; // Todo сегодняшнее число !?
                    $newCampaign->startDate = $campaign->getStartDate();
                    $newCampaign->endDate = $campaign->getEndDate();
                    // Get campaign perfomance
                    $perf = $this->adwGetCampaignPerfomance($newCampaign->campaign_id, $newCampaign->customer_id);
                    if($perf){
                        $w = $perf['w'];
                        $m = $perf['m'];
                        $newCampaign->cost = round(($w->cost/1000000),2);
                        $newCampaign->conv = $w->conversions;
                        $newCampaign->conv_value = $w->totalConvValue;
                        $newCampaign->cost_30 = round(($m->cost/1000000),2);
                        $newCampaign->conv_30 = $m->conversions;
                        $newCampaign->conv_value_30 = $m->totalConvValue;
                        $newCampaign->save();

                    }
                    $newCampaign->save();
                });
            });
            return response()->json(['status' => true, 'message' => 'ok']);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Помилочка.' . $e]);
        }

    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCampaigns()
    {
        // Получаем список всех активных компаний
        return Campaign::with('customer', 'customer.account')
            ->whereHas('customer.account', function ($query) {
                return $query->where('active', '=', 1);
            })
            ->whereHas('customer', function ($query) {
                return $query->where('active', '=', 1);
            })
            ->where('active', '=', '1')
            ->get();
    }

    /**
     * @param $campaign_id
     * @return mixed
     */
    public function getSignleCampaign($campaign_id)
    {
        return Campaign::where('id', '=', $campaign_id)->with('customer', 'customer.account')
            ->whereHas('customer.account')
            ->whereHas('customer')
            ->first();
    }

    /**
     * @param $campaign
     * @return mixed
     */
    public function getCampaignAccount($campaign)
    {
        return $campaign->getRelations()['customer']->getRelations()['account']->getDecryptAll();
    }


    public function changeStatus($campaignID, Request $request)
    {
        $newStatus = $request->input('newStatus');
        try {
            // Получаем Customer для настроек api подключений к adwords
            $campaign = Campaign::where('id', '=', $campaignID)->first();
            $adwCampaignID = $campaign->campaign_id;
            $customerId = $campaign->customer_id;
            $customer = Customer::where('id', '=', $customerId)->first();
            $account = Account::where('id', '=', $customer->account_id)->first();
            // Создаём экземпляр класса AdwordsApiController
            $AdwordsApiController = new AdwordsApiController($account->getDecryptAll());
            // Setup
            $AdwordsApiController->setIsTesting($customer->account->testing);
            $AdwordsApiController->setClientCustomerId($customer->customer_id);
            // Делаем запрос в adwords
            $result = '';
            if ($newStatus == 'ENABLED') {
                $result = $AdwordsApiController->campaignEnabled($adwCampaignID);
                $campaign->status = 'ENABLED';
                $campaign->save();
            } else {
                $result = $AdwordsApiController->campaignPause($adwCampaignID);
                $campaign->status = 'PAUSED';
                $campaign->save();
            }


            return response()->json(['status' => true, 'message' => 'ok', 'result' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Помилочка.' . $e]);
        }

    }

    /*
     * Метод отправляет запрос на Adwords api для получение и обновления в системе статуса кампании...
     * PAUSED или ENABLED
     */
    public function adwGetCampaign($campaignID)
    {
        try {
            // Получаем Customer для настроек api подключений к adwords
            $campaign = Campaign::where('id', '=', $campaignID)->first();
            $adwCampaignID = $campaign->campaign_id;
            $customerId = $campaign->customer_id;
            $customer = Customer::where('id', '=', $customerId)->first();
            $account = Account::where('id', '=', $customer->account_id)->first();
            // Создаём экземпляр класса AdwordsApiController
            $AdwordsApiController = new AdwordsApiController($account->getDecryptAll());
            // Setup
            $AdwordsApiController->setIsTesting($customer->account->testing);
            $AdwordsApiController->setClientCustomerId($customer->customer_id);
            // Делаем запрос в adwords
            $campaign = $AdwordsApiController->getShoppingCampaign($adwCampaignID);
            return response()->json(['status' => true, 'message' => 'ok', 'data' => $campaign[0]->getStatus()]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Помилочка.' . $e]);
        }
    }

    public function adwGetCampaignPerfomance($adwCampaignID, $customerId)
    {
        try {
            $customer = Customer::where('id', '=', $customerId)->first();
            $account = Account::where('id', '=', $customer->account_id)->first();
            // Создаём экземпляр класса AdwordsApiController
            $AdwordsApiController = new AdwordsApiController($account->getDecryptAll());
            // Setup
            $AdwordsApiController->setIsTesting($customer->account->testing);
            $AdwordsApiController->setClientCustomerId($customer->customer_id);
            // Делаем запрос в adwords
            $weekPerf = $AdwordsApiController->getPerfomanceReportThisWeek($adwCampaignID)->result[0];
            $mounthPerf = $AdwordsApiController->getPerfomanceDateRange($adwCampaignID, 30, 1)->result[0];
            return ['w' => $weekPerf, 'm' => $mounthPerf];
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function campprefupdate(){
        // Not adw optimized for big requests!!!
        $campaigns = Campaign::all();
        $campaigns->each(function ($campaign) {
            if ($campaign->id > 100) {
                $perf = $this->adwGetCampaignPerfomance($campaign->campaign_id, $campaign->customer_id);
                if($perf){
                    $w = $perf['w'];
                    $m = $perf['m'];
                    $campaign->cost = round(($w->cost/1000000),2);
                    $campaign->conv = $w->conversions;
                    $campaign->conv_value = $w->totalConvValue;
                    $campaign->cost_30 = round(($m->cost/1000000),2);
                    $campaign->conv_30 = $m->conversions;
                    $campaign->conv_value_30 = $m->totalConvValue;
                    $campaign->save();
                }
            }

        });
    }
}





