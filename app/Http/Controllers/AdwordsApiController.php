<?php

// https://developers.google.com/adwords/api/docs/guides/mobile-app-campaigns

namespace App\Http\Controllers;

use App\Http\Traits\Helper;
use Edujugon\GoogleAds\GoogleAds;
use Google\AdsApi\AdWords\v201809\cm\AdvertisingChannelType;
use Google\AdsApi\AdWords\v201809\cm\Selector;
use Google\AdsApi\AdWords\v201809\cm\Campaign as CampaignAdw;
use Google\AdsApi\AdWords\v201809\cm\CampaignStatus;
use Google\AdsApi\AdWords\v201809\cm\Operator;
use Google\AdsApi\AdWords\v201809\cm\ShoppingSetting;
use Google\AdsApi\AdWords\v201809\cm\Budget;
use Google\AdsApi\AdWords\v201809\cm\Money;
use Google\AdsApi\AdWords\v201809\cm\BudgetBudgetDeliveryMethod;
use Google\AdsApi\AdWords\v201809\cm\CpcBid;

// Services
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\v201809\cm\CampaignCriterionService;
use Google\AdsApi\AdWords\v201809\cm\AdGroupCriterionService;
use Google\AdsApi\AdWords\v201809\mcm\ManagedCustomerService;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Google\AdsApi\AdWords\v201809\cm\BudgetService;

// Operations
use Google\AdsApi\AdWords\v201809\cm\CampaignOperation;
use Google\AdsApi\AdWords\v201809\cm\BudgetOperation;
use Google\AdsApi\AdWords\v201809\cm\CampaignCriterionOperation;
use Google\AdsApi\AdWords\v201809\cm\AdGroupCriterionOperation;

use Google\AdsApi\AdWords\v201809\cm\CampaignCriterion;
use Google\AdsApi\AdWords\v201809\cm\Location;



use Illuminate\Support\Carbon;

class AdwordsApiController
{
    // Работа с логами, и шифрованием
    use Helper;

    private $developerToken;
    private $clientCustomerId;
    private $userAgent;
    private $clientId;
    private $clientSecret;
    private $refreshToken;
    private $ads; // Instanse of adwords class

    public $logData;
    public $isTesting = true;

    /**
     * AdwordsApiController constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->developerToken = $settings['developer_token'];
        $this->userAgent = 'MY_NAME';
        $this->clientId = $settings['client_id'];
        $this->clientSecret = $settings['client_secret'];
        $this->refreshToken = $settings['refresh_token'];
    }

    /**
     * Init
     */
    public function init()
    {
        $this->ads = new GoogleAds();
        $this->ads->env('mainsettings')
            ->oAuth([
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'refreshToken' => $this->refreshToken,
            ])
            ->session([
                'developerToken' => $this->developerToken,
                'clientCustomerId' => $this->clientCustomerId,
            ]);
    }

    /**
     * @param $clientCustomerId
     */
    public function setClientCustomerId($clientCustomerId)
    {
        $this->clientCustomerId = $clientCustomerId;
        $this->init();
    }

    /**
     * @param $data
     */
    public function setLogData($data)
    {
        $this->logData['task_id'] = $data['task_id'];
        $this->logData['customer_id'] = $data['customer_id'];
    }

    /**
     * @param $isTesting
     */
    public function setIsTesting($isTesting)
    {
        $this->isTesting = $isTesting;
    }

    /**
     * @return mixed
     *
     * Метод возвращает все дочерние Адвордс аккаунты включая родителя.
     */
    public function getCustomers()
    {
        $selector = new Selector();
        $selector->setFields(['CustomerId', 'Name', 'CanManageClients']);
        $service = $this->ads->service(ManagedCustomerService::class)->getService();
        return $service->get($selector);
    }

    /**
     * @return mixed
     */
    public function getShoppingCampaigns($enabled = false)
    {
        /*
         * Получение списка только shopping компаний заданного адвордс аккаунта.
         */
        $data = $this->ads->service(CampaignService::class)->select([
            'Id',
            'AdvertisingChannelType',
            'Name',
            'Status',
            'ServingStatus',
            'StartDate',
            'EndDate',
            'BudgetId',
            'BudgetName',
            'Amount',
            'BudgetStatus',
            'BudgetReferenceCount',
            'BiddingStrategyType',
            'AdvertisingChannelSubType',
            'MaximizeConversionValueTargetRoas',
            'TargetRoas',
            'TargetRoasBidCeiling',
            'TargetRoasBidFloor'
        ])->where('AdvertisingChannelType = SHOPPING');
        if ($enabled) {
            $data = $data->where('Status = ENABLED');
        }
        return $data->get()->items();
    }

    public function getShoppingCampaign($adwCampaignId)
    {
        // Использует метод обновления статуса PAUSED, ENABLED
        /*
         * Получение списка только shopping компаний заданного адвордс аккаунта.
         */
        $data = $this->ads->service(CampaignService::class)->select([
            'Id',
            'AdvertisingChannelType',
            'Name',
            'Status',
            'ServingStatus',
            'StartDate',
            'EndDate',
            'BudgetId',
            'BudgetName',
            'Amount',
            'BudgetStatus',
            'BudgetReferenceCount',
            'BiddingStrategyType',
            'AdvertisingChannelSubType',
            'MaximizeConversionValueTargetRoas',
            'TargetRoas',
            'TargetRoasBidCeiling',
            'TargetRoasBidFloor'
        ])->where('AdvertisingChannelType = SHOPPING');
        $data = $data->where('Id ='.$adwCampaignId);
        return $data->get()->items();
    }

    /**
     * @param $campaignId
     * @return mixed
     */
    public function getSearchImpressionShare($campaignId)
    {
        $result = $this->ads->report()
            ->from('CAMPAIGN_PERFORMANCE_REPORT')
            ->where('CampaignId = ' . $campaignId)
            ->select('SearchImpressionShare')
            ->getAsObj()->result[0]->searchImprShare;
        return $result;
    }

    /**
     * @param $campaignId
     * @return mixed
     */
    public function getPerfomanceReportThisWeek($campaignId)
    {
        $startThisWeek = Carbon::now()->subDays(7)->format('Ymd');
        $now = Carbon::now()->subDays(1)->format('Ymd');
        $result = $this->ads->report()
            ->from('CAMPAIGN_PERFORMANCE_REPORT')
            ->where('CampaignId = ' . $campaignId)
            ->during($startThisWeek, $now)
            ->select('Clicks', 'Cost', 'ConversionValue', 'Impressions', 'Conversions')
            ->getAsObj();
        //$result = $this->ads->report()
        //    ->from('CRITERIA_PERFORMANCE_REPORT', 'CAMPAIGN_PERFORMANCE_REPORT')
        //    ->where('CampaignId = ' . $campaignId)
        //    ->during($startThisWeek, $now)
        //    ->select('CampaignId', 'AdGroupId', 'AdGroupName', 'Id', 'Criteria', 'CriteriaType', 'Impressions',
        //        'Clicks', 'Cost', 'UrlCustomParameters', 'ConversionValue', 'ConversionRate', 'CostPerConversion')
        //    ->getAsObj();
        return $result;
    }

    /**
     * @param $campaignId
     * @return mixed
     */
    public function getPerfomanceReportLastWeek($campaignId)
    {
        $start = Carbon::now()->subDays(14)->format('Ymd');
        $now = Carbon::now()->subDays(8)->format('Ymd');
        $result = $this->ads->report()
            ->from('CAMPAIGN_PERFORMANCE_REPORT')
            ->where('CampaignId = ' . $campaignId)
            ->during($start, $now)
            ->select('Clicks', 'Cost', 'ConversionValue', 'Impressions', 'Conversions')
            ->getAsObj();
        //$result = $this->ads->report()
        //    ->from('CRITERIA_PERFORMANCE_REPORT')
        //    ->where('CampaignId = ' . $campaignId)
        //    ->during($start, $now)
        //    ->select('CampaignId', 'AdGroupId', 'AdGroupName', 'Id', 'Criteria', 'CriteriaType', 'Impressions',
        //        'Clicks', 'Cost', 'UrlCustomParameters', 'ConversionValue', 'ConversionRate', 'CostPerConversion')
        //    ->getAsObj();
        return $result;
    }

    public function getPerfomanceDateRange($campaignId, $subStart, $subEnd)
    {
        $start = Carbon::now()->subDays($subStart)->format('Ymd');
        $now = Carbon::now()->subDays($subEnd)->format('Ymd');
        return $this->ads->report()
            ->from('CAMPAIGN_PERFORMANCE_REPORT')
            ->where('CampaignId = ' . $campaignId)
            ->during($start, $now)
            ->select('Clicks', 'Cost', 'ConversionValue', 'Impressions','Conversions')
            ->getAsObj();
    }

    /**
     * @param $id
     *
     * Метод остановки компании ( Установка компании на паузу )
     */
    public function campaignPause($campaignId)
    {
       if (!$this->isTesting) {
            $result = $this->ads->service(CampaignService::class)->select(['Id', 'Status'])
                ->get()
                ->where('id', $campaignId)->set('status', 'PAUSED')->save();
            return $result;
        }
    }

    /**
     * @param $id
     *
     * Метод запуска компании
     */
    public function campaignEnabled($campaignId)
    {
        if (!$this->isTesting) {
            $result = $this->ads->service(CampaignService::class)->select(['Id', 'Status'])
                ->get()
                ->where('id', $campaignId)->set('status', 'ENABLED')->save();
            return $result;
        }
    }

    /**
     * Operations with budget
     */

    public function increaseBudget($campaign, $percent, $maxBudget)
    {
        return $this->changeBudget($campaign, $percent, true, $maxBudget);
    }

    public function decreaseBudget($campaign, $percent)
    {
        return $this->changeBudget($campaign, $percent, false);
    }

    /**
     * @param $campaign
     * @param $percent
     */
    public function changeBudget($campaign, $percent, $increase = true, $maxBudget = 0)
    {
        // Logs
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => $increase ? 'Повышаем бюджет' : 'Понижаем бюджет',
            'message' => $percent . ' %'
        ]);

        // Получаем обьект бюджета
        $budget = $campaign->getBudget();
        // Устанавливаем новое значение бюджета
        $money = $budget->getAmount();
        // Получаем текущее значение бюджета
        $currentBudget = $money->getMicroAmount();
        // Получаем процент
        $percentValue = $currentBudget * ($percent / 100);
        // Новое значение бюджета
        if ($increase) {
            $newBudgetValue = intval($currentBudget + $percentValue);
        } else {
            $newBudgetValue = intval($currentBudget - $percentValue);
        }

        // Remove Digits
        $newBudgetValue = intval($newBudgetValue / 10000) * 10000;

        // Проверяем на не превышение бюджета
        if ($increase) {
            if ($newBudgetValue > $maxBudget * 1000000) {
                $newBudgetValue = $maxBudget * 1000000;
                // LOG
                $this->createLog([
                    'task_id' => $this->logData['task_id'],
                    'customer_id' => $this->logData['customer_id'],
                    'campaign_id' => $campaign->getId(),
                    'type' => 'warning',
                    'task' => $increase ? 'Повышаем бюджет' : 'Понижаем бюджет',
                    'message' => 'Превышение допустимого бюжета!! Новый бюджет : ' . $newBudgetValue . ' max_budget : ' . ($maxBudget * 1000000)
                ]);
            }
        }
        if (!$this->isTesting) {
            // Set
            $money->setMicroAmount($newBudgetValue);
            $budget->setAmount($money);
            // Save
            $operation = new BudgetOperation();
            $operation->setOperand($budget);
            $operation->setOperator(Operator::SET);
            $operations[] = $operation;
            $session = $this->ads->getSession();
            $AdWordsServices = new AdWordsServices();
            $budgetService = $AdWordsServices->get($session, BudgetService::class);
            $result = $budgetService->mutate($operations);
            $budget = $result->getValue()[0];
            $campaign->setBudget($budget);
        }

        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => $increase ? 'Повышаем бюджет' : 'Понижаем бюджет',
            'message' => 'Завершено!' . ' Старый бюджет : ' . $currentBudget . ' , Новый бюджет : ' . $newBudgetValue
        ]);

        return true;
    }

    /**
     * Changing CPC
     */

    public function increaseCPC($campaign, $percent)
    {
        $this->changeCPC($campaign, $percent);
    }

    public function decreaseCPC($campaign, $percent)
    {
        $this->changeCPC($campaign, $percent, false);
    }

    /**
     * @param $campaign
     * @param $percent
     * @return bool
     */
    public function changeCPC($campaign, $percent, $increase = true)
    {

        // На уровне product groups

        // Получаем групы
        $items = $this->ads->service(AdGroupCriterionService::class)->select(['CampaignId', 'CpcBid'])
            ->where('CampaignId = ' . $campaign->getId())->get()->items();



        $items->each(function ($item) use ($percent, $campaign, $increase) {

            // LOGAdGroupCriterionServiceAdGroupCriterionService
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaign->getId(),
                'type' => 'processed',
                'task' => $increase ? 'Увеличиваем CPC (Enhanced) c Optimized for conversion value)) на уровне product groups' : 'Уменьшаем CPC (Enhanced) c Optimized for conversion value)) на уровне product groups',
                'message' => ''
            ]);

            if (!method_exists($item, 'getBiddingStrategyConfiguration')) {
                echo($item->getAdGroupId() . ' ');
                return true;
            }

            // Получаем стратегию
            $biddingStrategyConfiguration = $item->getBiddingStrategyConfiguration();
            // На всякий случай переключаем её тип на None
            $biddingStrategyConfiguration->setBiddingStrategyType('NONE');
            // Получаем биды
            $bids = $biddingStrategyConfiguration->getBids();
            // Получаем первый

            if (!is_array($bids) || !isset($bids[0])) {
                // LOG
                $this->createLog([
                    'task_id' => $this->logData['task_id'],
                    'customer_id' => $this->logData['customer_id'],
                    'campaign_id' => $campaign->getId(),
                    'type' => 'processed',
                    'task' => $increase ? 'Увеличиваем CPC (Enhanced) c Optimized for conversion value)) на уровне product groups' : 'Уменьшаем CPC (Enhanced) c Optimized for conversion value)) на уровне product groups',
                    'message' => 'Бюджета не существует'
                ]);
                return true;
            }

            $bid = $bids[0]->getBid();
            // Получаем Amount
            $currentAmount = $bid->getMicroAmount();
            // Получаем значения для манипуляций
            $percentValue = $currentAmount * ($percent / 100);
            // Устанавливаем новое значение
            if ($increase) {
                $newAmount = $currentAmount + $percentValue;
            } else {
                $newAmount = $currentAmount - $percentValue;
            }

            // Remove Digits
            $newAmount = intval($newAmount / 10000) * 10000;

            // Set
            $bid->setMicroAmount($newAmount);

            // LOG
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaign->getId(),
                'type' => 'processed',
                'task' => $increase ? 'Увеличиваем CPC (Enhanced) c Optimized for conversion value)) на уровне product groups' : 'Уменьшаем CPC (Enhanced) c Optimized for conversion value)) на уровне product groups',
                'message' => $percent . ' % ( ' . $currentAmount . ' => ' . $newAmount . ' )'
            ]);
            if (!$this->isTesting) {
                // Save
                $operation = new AdGroupCriterionOperation();
                $operation->setOperand($item);
                $operation->setOperator(Operator::SET);
                $operations[] = $operation;

                $AdWordsServices = new AdWordsServices();
                $session = $this->ads->getSession();
                $adGroupService = $AdWordsServices->get($session, AdGroupCriterionService::class);

                $adGroupService->mutate($operations);
            }
        });

        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => $increase ? 'Увеличиваем CPC (Enhanced) c Optimized for conversion value)) на уровне product groups' : 'Уменьшаем CPC (Enhanced) c Optimized for conversion value)) на уровне product groups',
            'message' => 'Завершено!'
        ]);
        return true;
    }

    /**
     * Target Roas
     */

    public function changeTargetRoas($campaign, $newAmount)
    {
        // Метод меняет стратегию ставок!!!
        // На Target Roas
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Меняем стратегию ставок на Target Roas',
            'message' => $newAmount . ' %'
        ]);

        // Get bidding strategy configuration
        $biddingStrategyConfiguration = $campaign->getBiddingStrategyConfiguration();

        // Set new type of bidding
        $biddingStrategyConfiguration->setBiddingStrategyType('TARGET_ROAS');

        $bid = new CpcBid();
        $money = new Money();
        $money->setMicroAmount($newAmount);
        $bid->setBid($money);

        $biddingStrategyConfiguration->setBids([$bid]);

        // Change value api
        if (!$this->isTesting) {
            $operation = new CampaignOperation();
            $operation->setOperand($campaign);
            $operation->setOperator(Operator::SET);
            $operations[] = $operation;

            $AdWordsServices = new AdWordsServices();
            $session = $this->ads->getSession();
            $campaignService = $AdWordsServices->get($session, CampaignService::class);

            $campaignService->mutate($operations);
        }

        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Меняем стратегию ставок на Target Roas',
            'message' => 'Завершено!'
        ]);

        return true;

        /*
        TODO
        [OperationAccessDenied.OPERATION_NOT_PERMITTED_FOR_CAMPAIGN_TYPE @ operations[0].operand.biddingStrategyConfiguration.bids; trigger:'SHOPPING']
         */
    }

    /**
     * @param $campaign
     * @param $percent
     * @return bool
     */
    public function increaseTargetRoas($campaign, $percent)
    {
        // Log
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Увеличиваем Target Roas',
            'message' => $percent . ' %'
        ]);

        // Get bidding strategy configuration
        $biddingStrategyConfiguration = $campaign->getBiddingStrategyConfiguration();
        $bids = $biddingStrategyConfiguration->getBids();

        if (!isset($bids[0])) {
            return false;
        }
        if (!$this->isTesting) {
            $bid = $bids[0]->getBid();
            $currentAmount = $bid->getMicroAmount();
            $newAmount = $currentAmount + $percent;
            $bid->setMicroAmount($newAmount);

            $operation = new AdGroupCriterionOperation();
            $operation->setOperand($bid);
            $operation->setOperator(Operator::SET);
            $operations[] = $operation;

            $AdWordsServices = new AdWordsServices();
            $session = $this->ads->getSession();
            $adGroupService = $AdWordsServices->get($session, CampaignService::class);

            $adGroupService->mutate($operations);
        }

        // Log
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Увеличиваем Target Roas',
            'message' => 'Завершено!'
        ]);

        return true;
    }

    /**
     * @param $campaign
     * @param $percent
     * @return bool
     */
    public function decreaseTargetRoas($campaign, $percent)
    {
        // Проверка на минимальный target roas 200

        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Уменьшаем Target Roas',
            'message' => $percent . ' %'
        ]);

        // Get bidding strategy configuration
        $biddingStrategyConfiguration = $campaign->getBiddingStrategyConfiguration();
        $bids = $biddingStrategyConfiguration->getBids();

        if (!isset($bids[0])) {
            return false;
        }
        if (!$this->isTesting) {
            $bid = $bids[0]->getBid();
            $currentAmount = $bid->getMicroAmount();
            $newAmount = $currentAmount - $percent;
            if ($newAmount <= 200) {
                $newAmount = 200;
            }
            $bid->setMicroAmount($newAmount);

            $operation = new AdGroupCriterionOperation();
            $operation->setOperand($bid);
            $operation->setOperator(Operator::SET);
            $operations[] = $operation;

            $AdWordsServices = new AdWordsServices();
            $session = $this->ads->getSession();
            $adGroupService = $AdWordsServices->get($session, CampaignService::class);

            $adGroupService->mutate($operations);
        }

        // Log
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Уменьшаем Target Roas',
            'message' => 'Завершено!'
        ]);

        return true;
    }

    /**
     * Changing Maximize conversion value ROAS
     */

    /**
     * @param $campaign
     * @param $value
     * @return bool
     */
    public function setMaximizeConvValueRoas($campaign, $param)
    {
        $value = ($param == 'default') ? '0.0' : $param;
        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Устанавливаем maximize conversion value (ROAS)',
            'message' => $campaign->getBiddingStrategyConfiguration()->getBiddingScheme()->getTargetRoas() . ' => ' . $value
        ]);

        $targetRoasBiddingScheme = $campaign->getBiddingStrategyConfiguration()->getBiddingScheme();
        $targetRoasBiddingScheme->setTargetRoas($value);

        // Change value api
        if (!$this->isTesting) {
            $operation = new CampaignOperation();
            $operation->setOperand($campaign);
            $operation->setOperator(Operator::SET);
            $operations[] = $operation;

            $AdWordsServices = new AdWordsServices();
            $session = $this->ads->getSession();
            $campaignService = $AdWordsServices->get($session, CampaignService::class);

            $campaignService->mutate($operations);
        }

        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Устанавливаем maximize conversion value (ROAS): '. $param,
            'message' => 'Завершено!'
        ]);
        return true;
    }

    /**
     * @param $campaign
     * @param $percent
     * @return bool
     */
    public function increaseMaximizeConvValueRoas($campaign, $percent)
    {
        // Получаем текущее значение target roas
        // Внимание если он 0 тоесть default то ставится просто значение процента!
        $currentTargetRoas = $campaign->getBiddingStrategyConfiguration()->getBiddingScheme()->getTargetRoas();

        // Get percent value
        // $number_percent = $currentTargetRoas / 100 * $percent;

        // Получаем новое значение target roas
        $newTargetRoas = $currentTargetRoas + $percent; // прибавляем значение. Это не процент.

        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Повышаем maximize conversion value (ROAS)',
            'message' => $percent . ' => ( ' . $currentTargetRoas . ' => ' . $newTargetRoas . ' )'
        ]);

        // Обязательно проверяем, что biddingStrategyType = MAXIMIZE_CONVERSION_VALUE !!!
        if ($campaign->getBiddingStrategyConfiguration()->getBiddingStrategyType() != 'MAXIMIZE_CONVERSION_VALUE') {
            // LOG
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaign->getId(),
                'type' => 'warning',
                'task' => 'Повышаем maximize conversion value (ROAS)',
                'message' => 'bidding strategy type != MAXIMIZE_CONVERSION_VALUE!'
            ]);
            return false;
        }

        $targetRoasBiddingScheme = $campaign->getBiddingStrategyConfiguration()->getBiddingScheme();
        $targetRoasBiddingScheme->setTargetRoas($newTargetRoas);

        // Change value api
        if (!$this->isTesting) {
            $operation = new CampaignOperation();
            $operation->setOperand($campaign);
            $operation->setOperator(Operator::SET);
            $operations[] = $operation;

            $AdWordsServices = new AdWordsServices();
            $session = $this->ads->getSession();
            $campaignService = $AdWordsServices->get($session, CampaignService::class);

            $campaignService->mutate($operations);
        }

        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Повышаем maximize conversion value (ROAS)',
            'message' => 'Завершено!'
        ]);
        return true;
    }

    /**
     * @param $campaign
     * @param $percent
     * @return bool
     */
    public function decreaseMaximizeConvValueRoas($campaign, $percent, $minCheck = false, $setDefault = false)
    {

        // $setDefault = true значит если если значение будет меньше 300 то ставим 0 а не 300 !! Тоесть default
        // Получаем текущее значение target roas
        // Внимание если он 0 тоесть default то ставится просто значение процента!
        $currentTargetRoas = $campaign->getBiddingStrategyConfiguration()->getBiddingScheme()->getTargetRoas();

        // Get percent value
        // $number_percent = $currentTargetRoas / 100 * $percent;

        // Получаем новое значение target roas
        $newTargetRoas = $currentTargetRoas - $percent; // прибавляем значение. Это не процент.

        if ($newTargetRoas < 0) {
            $newTargetRoas = 0;
        }
        if ($minCheck) {
            $newTargetRoas = ($setDefault) ? 0 : 300;
        }

        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Понижаем maximize conversion value (ROAS)',
            'message' => $percent . ' => ( ' . $currentTargetRoas . ' => ' . $newTargetRoas . ' )'
        ]);

        // Обязательно проверяем, что biddingStrategyType = MAXIMIZE_CONVERSION_VALUE !!!
        if ($campaign->getBiddingStrategyConfiguration()->getBiddingStrategyType() != 'MAXIMIZE_CONVERSION_VALUE') {
            // LOG
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaign->getId(),
                'type' => 'warning',
                'task' => 'Понижаем maximize conversion value (ROAS)',
                'message' => 'bidding strategy type != MAXIMIZE_CONVERSION_VALUE!'
            ]);
            return false;
        }

        $targetRoasBiddingScheme = $campaign->getBiddingStrategyConfiguration()->getBiddingScheme();
        $targetRoasBiddingScheme->setTargetRoas($newTargetRoas);

        // Change value api
        if (!$this->isTesting) {
            $operation = new CampaignOperation();
            $operation->setOperand($campaign);
            $operation->setOperator(Operator::SET);
            $operations[] = $operation;

            $AdWordsServices = new AdWordsServices();
            $session = $this->ads->getSession();
            $campaignService = $AdWordsServices->get($session, CampaignService::class);

            $campaignService->mutate($operations);
        }

        // LOG
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Понижаем maximize conversion value (ROAS)',
            'message' => 'Завершено!'
        ]);
        return true;
    }

    /**
     * Other
     */

    /**
     * @param $campaignId
     * @param $standartAndSmartList
     */
    public function standartToSmart($campaign, $lnkCampaign)
    {

        if(!$lnkCampaign){
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaign->getId(),
                'type' => 'warning',
                'task' => 'Нет привязаной кампании',
                'message' => $campaign->getId() . ' => ' . $lnkCampaign
            ]);
        }

        // Log
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Отключаем стандарт компанию и включаем смарт',
            'message' => $campaign->getId() . ' => ' . $lnkCampaign
        ]);

        $this->campaignPause($campaign->getId());
        $this->campaignEnabled($lnkCampaign);
    }

    /**
     * @param $campaignId
     * @param $standartAndSmartList
     */
    public function smartToStandart($campaign, $lnkCampaign)
    {

        if(!$lnkCampaign){
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaign->getId(),
                'type' => 'warning',
                'task' => 'Нет привязаной кампании',
                'message' => $campaign->getId() . ' => ' . $lnkCampaign
            ]);
        }

        // Log
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Отключаем смарт компанию и включаем стандарт',
            'message' => $campaign->getId() . ' => ' . $lnkCampaign
        ]);

        $this->campaignPause($campaign->getId());
        $this->campaignEnabled($lnkCampaign);
    }

    /**
     * Creating campaign
     */
    public function createCampaign($customerId)
    {

        // TODO
        // Нужно запустить две компании Standart и Shopping

        // Log
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $customerId,
            'campaign_id' => '',
            'type' => 'processed',
            'task' => 'Создаём компании Standart и Shopping',
            'message' => 'Аккаунт : ' . $customerId
        ]);

        // Initialization
        $this->init();
        $session = $this->ads->getSession();
        $AdWordsServices = new AdWordsServices();

        // Campaign service
        $campaignService = $AdWordsServices->get($session, CampaignService::class);

        // Budget service
        $budgetService = $AdWordsServices->get($session, BudgetService::class);

        /**
         * Budget service block
         */

        $budget = new Budget();
        $budget->setName('Interplanetary Cruise Budget #' . uniqid());
        $money = new Money();
        $money->setMicroAmount(50000000); // Todo это нужно разобрать!
        $budget->setAmount($money); // Todo
        $budget->setDeliveryMethod(BudgetBudgetDeliveryMethod::STANDARD); // Todo

        $operations = [];
        // Create a budget operation.
        $operation = new BudgetOperation();
        $operation->setOperand($budget);
        $operation->setOperator(Operator::SET);
        $operations[] = $operation;

        // Create the budget on the server.
        $result = $budgetService->mutate($operations);
        $budget = $result->getValue()[0];

        // dd($budget);
        $budgetId = $budget->getBudgetId();

        /**
         *
         * Campaign service block
         *
         */

        // Create a campaign with required and optional settings.
        $campaign = new CampaignAdw();
        $campaign->setName('Shopping campaign #' . uniqid());
        // The advertisingChannelType is what makes this a Shopping campaign
        $campaign->setAdvertisingChannelType(AdvertisingChannelType::SHOPPING);
        // Recommendation: Set the campaign to PAUSED when creating it to stop
        // the ads from immediately serving. Set to ENABLED once you've added
        // targeting and the ads are ready to serve.
        $campaign->setStatus(CampaignStatus::PAUSED);

        // Set portfolio budget (required).
        // TODO
        // $campaign->setBudget(new Budget());
        // $campaign->getBudget()->setBudgetId($budgetId);

        // All Shopping campaigns need a ShoppingSetting.
        $shoppingSetting = new ShoppingSetting();
        $shoppingSetting->setSalesCountry('US');
        $shoppingSetting->setCampaignPriority(0);
        $shoppingSetting->setMerchantId('1');
        // TODO
        // $shoppingSetting->setMerchantId($merchantId);
        // Set to "true" to enable Local Inventory Ads in your campaign.
        $shoppingSetting->setEnableLocal(true);
        $campaign->setSettings([$shoppingSetting]);
        // Set portfolio budget (required).
        $campaign->setBudget(new Budget());
        $campaign->getBudget()->setBudgetId($budgetId);

        // Create a campaign operation and add it to the operations list.
        $operations = [];
        $operation = new CampaignOperation();
        $operation->setOperand($campaign);
        $operation->setOperator(Operator::ADD);
        $operations[] = $operation;

        $campaign = $campaignService->mutate($operations)->getValue()[0];
        printf(
            "Campaign with name '%s' and ID %d was added.\n",
            $campaign->getName(),
            $campaign->getId()
        );
    }

    /**
     * Changing campaign params
     */

    public function SwitchToOptimizeForConversionValue($campaignObject)
    {
        try {
            $biddingStrategyType = $campaignObject->getBiddingStrategyConfiguration()->getBiddingStrategyType();
            // 1 Должен быть biddingStrategyConfiguration -> biddingStrategyType -> MANUAL_CPC ( Работает )
            $biddingStrategyConfiguration = $campaignObject->getBiddingStrategyConfiguration();
            if ($biddingStrategyType != "MANUAL_CPC") {
                if (!$this->isTesting) {
                    $biddingStrategyConfiguration->setBiddingStrategyType('MANUAL_CPC');
                    // Enhanced CPC должен ставиться автоматом в true
                    // Change value api
                        $operation = new CampaignOperation();
                        $operation->setOperand($campaignObject);
                        $operation->setOperator(Operator::SET);
                        $operations[] = $operation;
                        $AdWordsServices = new AdWordsServices();
                        $session = $this->ads->getSession();
                        $campaignService = $AdWordsServices->get($session, CampaignService::class);
                        $campaignService->mutate($operations);
                }
                // Меняем на manual_cpc
            }
            // Log
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaignObject->getId(),
                'type' => 'processed',
                'task' => 'Изменяем настройки компании! SwitchToOptimizeForConversionValue',
                'message' => ''
            ]);
        } catch (\Throwable $e) {
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaignObject->getId(),
                'type' => 'processed',
                'task' => 'Изменяем настройки компании! SwitchToOptimizeForConversionValue',
                'message' => 'Ошибка!' . $e->getMessage()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Unselected methods
     */

    public function changeCampaignParams($campaign, $params)
    {
        // Log
        $this->createLog([
            'task_id' => $this->logData['task_id'],
            'customer_id' => $this->logData['customer_id'],
            'campaign_id' => $campaign->getId(),
            'type' => 'processed',
            'task' => 'Изменяем настройки компании!',
            'message' => ''
        ]);
        try {
            if (isset($params['location']) && $params['location']) {
                // Получаем весь список $campaignCriterionItems
                $campaignCriterionItems = $this->ads->service(CampaignCriterionService::class)->select([
                    'LocationName',
                    'CampaignId',
                    'CriteriaType'
                ])->get()
                    ->where('CampaignId', $campaign->getId())
                    ->items();

                if (is_array($campaignCriterionItems) && !empty($campaignCriterionItems)) {
                    // $campaignCriterionItems существует и нужно перебрать все и найти тот который связан с локацией.
                    // То есть criterion = Location
                    foreach ($campaignCriterionItems as &$campaignCriterionItem) {
                        $criterion = $campaignCriterionItem->getCriterion();
                        // Нахдим criterion с типом Location
                        // Сначала нужно всё поудалять! Менять нельзя только добавлять! По этому занимаемся хернёй!
                        if ($criterion->getCriterionType() == "Location") {
                            $operation = new CampaignCriterionOperation();
                            $operation->setOperand($campaignCriterionItem);
                            $operation->setOperator(Operator::REMOVE);
                            $operations[] = $operation;
                            $session = $this->ads->getSession();
                            $AdWordsServices = new AdWordsServices();
                            $campaignCriterionService = $AdWordsServices->get($session,
                                CampaignCriterionService::class);
                            $campaignCriterionService->mutate($operations);
                        }
                    };
                }
                $location = new Location();
                $location->setId(2804); // 2804 -> Urkaine

                $campaignCriterion = new CampaignCriterion();
                $campaignCriterion->setCampaignId($campaign->getId());
                $campaignCriterion->setCriterion($location);

                $operation = new CampaignCriterionOperation();
                $operation->setOperand($campaignCriterion);
                $operation->setOperator(Operator::ADD);
                $operations[] = $operation;
                $session = $this->ads->getSession();
                $AdWordsServices = new AdWordsServices();
                $campaignCriterionService = $AdWordsServices->get($session, CampaignCriterionService::class);
                $campaignCriterionService->mutate($operations);
            }
        } catch (\Throwable $e) {
            $this->createLog([
                'task_id' => $this->logData['task_id'],
                'customer_id' => $this->logData['customer_id'],
                'campaign_id' => $campaign->getId(),
                'type' => 'processed',
                'task' => 'Изменяем настройки компании!',
                'message' => 'Ошибка!' . $e->getMessage()
            ]);
        }
        return true;
    }

}