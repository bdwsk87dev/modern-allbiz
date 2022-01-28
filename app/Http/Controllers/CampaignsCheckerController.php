<?php

namespace App\Http\Controllers;

use App\Http\Traits\Helper;
use App\Models\Account;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\TaskLog;
use App\Models\DailyTaskLogs;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;

// Для проверки тут две компании, одна smart другая нет 1812391086 [10199361782,10454134484 и 10455359236 без target roas]

// Не стоит галочка Set a target return on ad spend (ROAS) ! Не ставит последнюю дату проверки если такая компания одна!

// В 4м блоке нужно скорее всего поставить MAXIMIZE_CONVERSION_VALUE если его нет

// Разобраться со стартовым набором компаний

// Разобраться с проверялась / не проверялась. Вторая итерация уже считается что проверялась

use function PHPUnit\Framework\isEmpty;

// Проверка на активные customers в обоих случаях
// Проверка на startDate и EndDate и в первом и во втором случае
// Проверка компании на Enabled, Disabled

class CampaignsCheckerController
{
    // Подключен трейт для работы с логами. Работа с логами, и шифрованием.
    use Helper;

    // Текущая компания, которая сейчас на проверке
    public $currentCmp = [];
    // Статистика текущей компании за предыдущую неделю
    public $lastWeekCmp = [];
    // Статистика текущей компании за текущую ( прошедшею ) неделю
    public $thisWeekCmp = [];
    // Сам обьект компании для манипуляций.
    public $objectCmp = [];
    // Id текущей проверки
    public $task_id;
    // Список стандартных и смарт компаний для переключений
    public $standartAndSmartList;
    // Контроллер для работы с API
    public $AdwordsApiController;

    public function start()
    {
        // Получаем id последней проверки
        $this->task_id = TaskLog::max('task_id');
        // Получаем id текущей проверки
        $this->task_id = (is_null($this->task_id)) ? 1 : ++$this->task_id;
        // Запускаем проверку
        $this->checkCustomersCampaigns($this->getCustomers());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCustomers()
    {
        /*
         * Метод получает из базы данных приложения список customers с учётом даты.
         * Если прошло 7 дней или дата последней проверки is null. Что значит что компании в этом
         * customers ещё не проверялись.
         *
         * Но нужно учитывать ещё один фактор. Что компании были только что созданы тогда значение тоже
         * будет is null. Эту проверку мы добавим при распределении компаний по блокам. "has_new_campaigns"
         */

        $result = Customer::with('account')
            ->whereHas('account', function ($query) {
                return $query->where('active', '=', 1);
            })
            ->whereHas('account', function ($query) {
                return $query->where('seven_days', '=', 1)->where('last_check_date', '<', Carbon::now()->subDays(7)->format('Ymd'));
            })
            ->orWhereHas('account', function ($query) {
                return $query->where('seven_days', '=', 0);
            })
            ->where('active', '=', '1');
        return $result->get();
    }

    public function checkCustomersCampaigns($customers)
    {
        // Еженедельная проверка

        /* logs */
        $this->createLog([
            'task_id' => $this->task_id,
            'type' => 'processed',
            'task' => 'Запуск проверки аккаунтов Customers',
            'message' => ''
        ]);
        // Проверяем список клиентов ( Customers ) на пустоту
        if ($customers->isEmpty()) {
            /* logs */
            // Если список аккаунтов для проверки пуст, выходим из метода
            $this->createLog([
                'task_id' => $this->task_id,
                'type' => 'warning',
                'task' => 'Запуск проверки аккаунтов (Customers)',
                'message' => 'Список аккаунтов пуст'
            ]);
            return false;
        }

        /**
         * Проверяем каждый адвордс аккаунт ( Customer ) по отдельности
         */

        $customers->each(function ($customer) {

            /* logs */
            $this->createLog([
                'task_id' => $this->task_id,
                'customer_id' => $customer->customer_id,
                'type' => 'processed',
                'task' => 'Найден новый customer ( адвордс клиент )',
                'message' => ''
            ]);
            /* Получаем api настройки текущего адвордс аккаунта */
            $settings = $customer->getRelation('account')->getDecryptAll();
            try {
                /* Api настройки */
                $this->AdwordsApiController = new AdwordsApiController($settings);
                $this->AdwordsApiController->setClientCustomerId($customer->customer_id);

                /* Получаем список копманий данного аккаунта */
                $campaigns = $this->AdwordsApiController->getShoppingCampaigns();

                if ($campaigns->isEmpty()) {
                    // Если компаний нет вообще, нужно создать!
                    /* logs */
                    $this->createLog([
                        'task_id' => $this->task_id,
                        'customer_id' => $customer->customer_id,
                        'type' => 'warning',
                        'task' => 'Получение списка компаний текущего аккаунта (customer)',
                        'message' => 'Внимание компаний в данном аккаунте нет!'
                    ]);

                    // TODO создание компаний, если их нет

                    //$this->AdwordsApiController->setLogData([
                    //    'task_id' => $this->task_id,
                    //    'customer_id' => $customer->customer_id
                    //]);
                    //$this->createCampaigns($customer->customer_id);

                    return true;
                }

                // Для логов. Тут содержатся все компании аккаунта вообще!
                $logCampaigns = [];
                // Список стандарт и смарт shopping компаний аккаунта
                $this->standartAndSmartList = ['standart' => [], 'smart' => []];

                // Перебераем все компании аккаунта ( shopping )
                $campaigns->each(function ($campaign) use (&$logCampaigns, &$logCampaign, $customer) {

                    // Обновляем значения обьекта текущей проверяемой компании
                    $this->currentCmp = [
                        'phase' => false,
                        'customerId' => $customer->customer_id,
                        'taskId' => $this->task_id
                    ];

                    // Обновляем параметр который содержит обьект adwords проверяемой компании
                    $this->objectCmp = $campaign;

                    // Получаем id компании
                    $this->currentCmp['campaignId'] = $this->objectCmp->getId();

                    // Тип компании ( Search или Shopping например )
                    $this->currentCmp['campaignType'] = $this->objectCmp->getAdvertisingChannelType();
                    // Получаем подтип компании является ли компания Smart
                    $this->currentCmp['advertisingChannelSubType'] = $this->objectCmp->getAdvertisingChannelSubType();

                    // Смотрим, текщая компания smart или нет
                    $this->currentCmp['isSmart'] = ($this->currentCmp['advertisingChannelSubType'] == 'SHOPPING_GOAL_OPTIMIZED_ADS') ? true : false;

                    // Получаем статус компании.( активна компания или нет )
                    $this->currentCmp['status'] = $this->objectCmp->getStatus();

                    /* Для логов */
                    array_push($logCampaigns, [
                        'id' => $this->currentCmp['campaignId'],
                        'type'=>$this->currentCmp['campaignType'],
                        'isSmart' => $this->currentCmp['isSmart'],
                        'status'=>$this->currentCmp['status']]);

                    // Разбираем какие компании стандарт а какие смарт для отключения и переключения
                    if($this->currentCmp['isSmart']) {
                        array_push($this->standartAndSmartList['smart'], $this->currentCmp['campaignId']);
                    }
                    else{
                        array_push($this->standartAndSmartList['standart'], $this->currentCmp['campaignId']);
                    }

                    // Делаем проверку на нужную нам компанию... Берём для работы только активные компании и те которые shopping
                    if ($this->currentCmp['status'] != "ENABLED" ||  $this->currentCmp['campaignType'] != "SHOPPING") {
                        return true; // Это Continue для foreach другими словами
                    }

                    // Проверялась ли компания ранее ( внимание проверку делаю по customer )
                    $this->currentCmp['customerFirstCheck'] = empty($customer->last_check_date);
                    // Start budget
                    $this->currentCmp['start_budget'] = $customer->start_budget;
                    // Max budget
                    $this->currentCmp['max_budget'] = $customer->max_budget;
                    // Получаем стратегию компании
                    $this->currentCmp['biddingStrategyType'] = $this->objectCmp->getBiddingStrategyConfiguration()->getBiddingStrategyType();

                    /* Статистика за эту неделю */
                    $this->thisWeekCmp = $this->AdwordsApiController->getPerfomanceReportThisWeek($this->currentCmp['campaignId'])->result[0];
                    /* Статистика за предыдущую неделю */
                    $this->lastWeekCmp = $this->AdwordsApiController->getPerfomanceReportLastWeek($this->currentCmp['campaignId'])->result[0];

                    // Search impression
                    $this->currentCmp['searchImpressionShare'] = ($this->AdwordsApiController->getSearchImpressionShare($this->currentCmp['campaignId']) == "--") ? '0' : $this->AdwordsApiController->getSearchImpressionShare($this->currentCmp['campaignId']);

                    // Получаем Roas
                    $this->currentCmp['roas'] = $this->thisWeekCmp->cost == 0 ? 0 : $this->thisWeekCmp->totalConvValue / ( $this->thisWeekCmp->cost / 1000000 );
                    // Получаем Roas за прошедшую неделю
                    $this->currentCmp['zRoas'] = $this->lastWeekCmp->cost == 0 ? 0 : $this->lastWeekCmp->totalConvValue/ ( $this->lastWeekCmp->cost / 1000000 );

                    // Определяем фазу компании
                    // Phase 1
                    if(!$this->currentCmp['isSmart'] && $customer->has_new_campaigns){
                        // Todo Убрать параметр has_new_campaigns только в самом конце в случае, если всё хорошо
                        // Todo или же оставить старый phase
                        $this->currentCmp['phase'] = 1;
                    }
                    // Phase 2 +Standard shopping ?Maximize Clicks, ?Target Imp. share, ?manual CPC
                    if(!$this->currentCmp['isSmart'] && ($this->currentCmp['biddingStrategyType'] == "MANUAL_CPC" || $this->currentCmp['biddingStrategyType'] == "TARGET_SPEND")) $this->currentCmp['phase'] = 2;
                    // Phase 3 +Standart shopping +Target Roas
                    if(!$this->currentCmp['isSmart'] && $this->currentCmp['biddingStrategyType'] == "TARGET_ROAS") $this->currentCmp['phase'] = 3;
                    // Phase 4 +Smart shopping +Maximize Conv. Value
                    if($this->currentCmp['isSmart'] && $this->currentCmp['biddingStrategyType'] == "MAXIMIZE_CONVERSION_VALUE") $this->currentCmp['phase'] = 4;
                    // Phase 4 ( Доп. операции с компаниями типа smart )
                    if($this->currentCmp['isSmart']){
                        $this->currentCmp['maximizeConvValueRoasIsDefault'] = true;
                        if(is_null($campaign->getBiddingStrategyConfiguration()->getBiddingScheme())){
                            // Не стоит галочка "Set a target return on ad spend (ROAS)"
                            $this->createLog([
                                'task_id' => $this->task_id,
                                'customer_id' => $customer->customer_id,
                                'campaign_id' => $campaign->getId(),
                                'type' => 'warning',
                                'task' => 'Проверка входных параметров смарт компании',
                                'message' => 'Не стоит галочка Set a target return on ad spend (ROAS). Ставлю maximizeConvValueRoasIsDefault = true!'
                            ]);
                            $this->currentCmp['maximizeConvValueRoasIsDefault'] = true;
                            //return true;
                        }
                        else{
                            $maximizeConvValueRoasValue = $campaign->getBiddingStrategyConfiguration()->getBiddingScheme()->getTargetRoas();
                            if($maximizeConvValueRoasValue != "0.0"){
                                $this->currentCmp['maximizeConvValueRoasIsDefault'] = false;
                                // TODO a1 ???
                                $this->currentCmp['maximizeConvValueRoasValue'] = $maximizeConvValueRoasValue;
                            }
                        }
                    }

                    // Log ( логируем компанию до каких-либо изменений )
                    $this->createLog([
                        'task_id' => $this->task_id,
                        'customer_id' => $customer->customer_id,
                        'campaign_id' => $this->currentCmp['campaignId'],
                        'type' => 'processed',
                        'task' => 'Проверка компании',
                        'message' => json_encode($this->currentCmp)." : this week -> ".json_encode($this->thisWeekCmp)." : last week -> ".json_encode($this->lastWeekCmp)
                    ]);

                    // Подготавливаем AdwordsApiController для работы. Устанавливаем все необходимые значения
                    // Передаём id задачи и customer для логов
                    $this->AdwordsApiController->setLogData([
                        'task_id' => $this->task_id,
                        'customer_id' => $customer->customer_id
                    ]);
                    // Передаём в AdwordsApiController является ли Аккаунт этого сервиса Тестовым
                    $this->AdwordsApiController->setIsTesting($customer->account->testing);

                    // Смотрим в каком блоке находится компания. В какой этап проверки её нужно передать
                    switch ($this->currentCmp['phase']) {
                        case '1':
                            $this->blockOne();
                            break;
                        case '2':
                            $this->blockTwo();
                            break;
                        case '3':
                            $this->blockThree();
                            break;
                        case '4':
                            $this->blockFourth();
                            break;
                        default:
                            return false;
                            break;
                    }
                });

                $this->createLog([
                    'task_id' => $this->task_id,
                    'customer_id' => $customer->customer_id,
                    'type' => 'processed',
                    'task' => 'Список всех компаний акканта (customer)',
                    'message' => json_encode($logCampaigns)
                ]);
            } catch (\Throwable $e) {
                /* logs */
                $this->createLog([
                    'task_id' => $this->task_id,
                    'customer_id' => $customer->customer_id,
                    'type' => 'error',
                    'task' => 'Получение списка компаний',
                    'message' => $e->getMessage().' '.$e->getLine()
                ]);
                return true;
            }
        });
    }

    public function blockOne()
    {
        // Информация о компании для сравнения
        $campaign = $this->currentCmp;
        // Обьект компании для манипуляций
        $campaignObject = $this->objectCmp;
        // Статистика за эту неделю
        $thisWeekCmp = $this->thisWeekCmp;
        // Статистика за предыдущую неделю
        $lastWeekCmp = $this->lastWeekCmp;
        // Адвордс апи контроллер
        $AdwordsApiController = $this->AdwordsApiController;

        // Log
        $this->createLog([
            'task_id' => $this->task_id,
            'customer_id' => $campaign['customerId'],
            'campaign_id' => $campaign['campaignId'],
            'type' => 'processed',
            'task' => 'Запуск фазы 1',
            'message' => ($campaign['customerFirstCheck']) ? 'Не проверялась' : 'Уже проверялась'
        ]);

        if ($thisWeekCmp->cost >= $campaign['start_budget'] * 7) {
            if ($campaign['roas'] >= 3) {
                if ($campaign['searchImpressionShare'] > 15) {
                    // Увеличиваем Budget на 30% (проверка на непревышение MaxBudget)
                    if($AdwordsApiController->increaseBudget($campaignObject, 30, $campaign['max_budget'])){
                        // OK change phase
                        $this->changeCampaignPhase(2);
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(1);
                    return true;
                }
                if ($campaign['searchImpressionShare'] < 15) {
                    // Увеличиваем Budget на 20% (проверка на не превышение MaxBudget)
                    // Увеличиваем CPC (Enhanced) c Optimized forconversion value)) на уровне product groups на 15%.
                    if($AdwordsApiController->increaseBudget($campaignObject, 20, $campaign['max_budget'])){
                        if($AdwordsApiController->increaseCPC($campaignObject, 15)){
                            // OK change phase
                            $this->changeCampaignPhase(2);
                            return true;
                        }
                    }
                    // Error change phase
                    $this->changeCampaignPhase(1);
                    return true;
                }
            }

            if ($campaign['roas'] < 3) {
                if ($campaign['searchImpressionShare'] > 15) {
                    // Уменьшаем CPC (Enhanced) cOptimized for conversionvalue)) на уровне productgroups на 20%
                    if($AdwordsApiController->increaseCPC($campaignObject, 20)){
                        // OK change phase
                        $this->changeCampaignPhase(2);
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(1);
                    return true;
                }
                if ($campaign['searchImpressionShare'] < 15) {
                    // Изменяем стратегию ставок для кампании на Target ROAS 300%
                    if($AdwordsApiController->changeTargetRoas($campaignObject, 300)){
                        // OK change phase
                        $this->changeCampaignPhase(3); // 7 days Normal
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(1);
                    return false;
                }
            }
        }

        if ($thisWeekCmp->cost <= $campaign['start_budget'] * 7) {
            if ($campaign['roas'] >= 3) {
                if ($campaign['searchImpressionShare'] > 15) {
                    // Увеличиваем CPC (Enhanced) c Optimized forconversion value)) на уровнеproduct groups на 10%
                    if($AdwordsApiController->increaseCPC($campaignObject, 10)){
                        // OK change phase
                        $this->changeCampaignPhase(2);
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(1);
                    return true;
                }
                if ($campaign['searchImpressionShare'] < 15) {
                    // Увеличиваем CPC (Enhanced) c Optimized for conversionvalue)) на уровне productgroups на 20%
                    if($AdwordsApiController->increaseCPC($campaignObject, 20)){
                        // OK change phase
                        $this->changeCampaignPhase(2);
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(1);
                    return true;
                }
            }
            if ($campaign['roas'] < 3) {
                if ($campaign['searchImpressionShare'] > 15) {
                    // Изменяем стратегию ставокдля кампании на Target ROAS 300%
                    if($AdwordsApiController->changeTargetRoas($campaignObject, 300)){
                        // OK change phase
                        $this->changeCampaignPhase(3); // 7 Days Normal!
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(1);
                    return true;
                }
                if ($campaign['searchImpressionShare'] < 15) {
                    // Увеличиваем CPC (Enhanced)c Optimized for conversionvalue)) на уровне productgroups на 10%
                    if($AdwordsApiController->increaseCPC($campaignObject, 10)){
                        // OK change phase
                        $this->changeCampaignPhase(2);
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(1);
                    return true;
                }
            }
        }
    }

    public function blockTwo()
    {
        // Информация о компании для сравнения
        $campaign = $this->currentCmp;
        // Обьект компании для манипуляций
        $campaignObject = $this->objectCmp;
        // Статистика за эту неделю
        $thisWeekCmp = $this->thisWeekCmp;
        // Статистика за предыдущую неделю
        $lastWeekCmp = $this->lastWeekCmp;
        // Адвордс апи контроллер
        $AdwordsApiController = $this->AdwordsApiController;

        // Log
        $this->createLog([
            'task_id' => $this->task_id,
            'customer_id' => $campaign['customerId'],
            'campaign_id' => $campaign['campaignId'],
            'type' => 'processed',
            'task' => 'Запуск фазы 2',
            'message' => ($campaign['customerFirstCheck']) ? 'Не проверялась' : 'Уже проверялась'
        ]);

        if($campaign['customerFirstCheck']){
            // Bidding (CPC (Enhanced) c Optimized for conversion value )

            if( $AdwordsApiController->SwitchToOptimizeForConversionValue($campaignObject) ){
                // На уровне product groups cpc на 15 %
                $AdwordsApiController->decreaseCPC($campaignObject, 15);
            }
        }

        if ($campaign['roas'] >= $campaign['zRoas']) {
            if ($thisWeekCmp->cost >= $lastWeekCmp->cost ) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks ) {
                    // Увеличиваем PrevBudget на 30% (проверка на не превышение MaxBudget)
                    $AdwordsApiController->increaseBudget($campaignObject, 30, $campaign['max_budget']);
                    // Во всех случаях
                    $this->changeCampaignPhase(2, '2-1-1-1');
                    return true;
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    // В этом случае клики стали в диапазоне 50 - 100% от прошлой недели
                    // Уменьшаем CPC (Enhanced) c Optimized forconversion value)) на уровне product groups на 10%.
                    // Увеличиваем PrevBudget на 15% (проверка на не превышение MaxBudget)
                    if($AdwordsApiController->increaseBudget($campaignObject, 15, $campaign['max_budget'])){
                        $AdwordsApiController->decreaseCPC($campaignObject, 10, '2-1-1-2');
                    }
                    // Во всех случаях
                    $this->changeCampaignPhase(2);
                    return true;

                } elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    // В этом случае клики стали в диапазоне 0 - 50% от прошлой недели
                    // Уменьшаем CPC (Enhanced) cOptimized for conversionvalue)) на уровне productgroups на 20%
                    $AdwordsApiController->decreaseCPC($campaignObject, 20);
                    // Во всех случаях
                    $this->changeCampaignPhase(2, '2-1-1-3');
                    return true;
                }
            }
            if ($thisWeekCmp->cost < $lastWeekCmp->cost ) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    // >>> Оставляем как есть без оптимизации
                    $this->changeCampaignPhase(2, '2-1-2-1');
                    return true; 
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    // В этом случае клики стали в диапазоне 50 - 100% от прошлой недели
                    // Увеличиваем CPC (Enhanced) c Optimized for conversionvalue )) на уровне productgroups на 10%
                    $AdwordsApiController->increaseCPC($campaignObject, 10);
                    // Во всех случаях
                    $this->changeCampaignPhase(2, '2-1-2-2');
                    return true;
                } elseif ($thisWeekCmp->clicks  <= ($lastWeekCmp->clicks / 2)) {
                    // В этом случае клики стали в диапазоне 0 - 50% от прошлой недели
                    // Увеличиваем CPC (Enhanced) c Optimized for conversionvalue )) на уровне productgroups на 20%
                    $AdwordsApiController->increaseCPC($campaignObject, 20);
                    // Во всех случаях
                    $this->changeCampaignPhase(2, '2-1-2-3');
                    return true;
                }
            }
        }

        if ($campaign['roas'] < $campaign['zRoas']) {
            if ($thisWeekCmp->cost >= $lastWeekCmp->cost ) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    // Уменьшаем CPC (Enhanced) cOptimized for conversion value)) науровне product groups на 15%.
                    $AdwordsApiController->decreaseCPC($campaignObject, 15);
                    // Уменьшаем PrevBudget на 10%
                    $AdwordsApiController->decreaseBudget($campaignObject, 10);
                    // Во всех случаях
                    $this->changeCampaignPhase(2, '2-2-1-1');
                    return true;
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    // В этом случае клики стали в диапазоне 50 - 100% от прошлой недели
                    // Изменяем стратегию ставокдля кампании на Target ROAS 300%
                    if ($AdwordsApiController->changeTargetRoas($campaignObject, 300)){
                        // OK change phase
                        $this->changeCampaignPhase(3, '2-2-1-2');
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(2);
                    return true;
                } elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    // В этом случае клики стали в диапазоне 0 - 50% от прошлой недели
                    if ($AdwordsApiController->changeTargetRoas($campaignObject, 300)){
                        // OK change phase
                        $this->changeCampaignPhase(3, '2-2-1-3');
                        return true;
                    }
                    // Error change phase
                    $this->changeCampaignPhase(2, '2-2-1-3');
                    return true;
                }
            }
            if ($thisWeekCmp->cost < $lastWeekCmp->cost ) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    // >>> Изменяем стратегию ставокдля кампании на TargetROAS 300%
                    if ($AdwordsApiController->changeTargetRoas($campaignObject, 300)){
                        $this->blockThree();
                    }
                    // Error change phase
                    $this->changeCampaignPhase(2, '2-2-2-1');
                    return true;
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    // В этом случае клики стали в диапазоне 50 - 100% от прошлой недели
                    // Увеличиваем CPC (Enhanced)c Optimized for conversionvalue)) на уровне productgroups на 10%
                    $AdwordsApiController->increaseCPC($campaignObject, 10);
                    // Во всех случаях
                    $this->changeCampaignPhase(2, '2-2-2-2');
                    return true;
                } elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    // В этом случае клики стали в диапазоне 0 - 50% от прошлой недели
                    // Увеличиваем CPC (Enhanced)c Optimized for conversionvalue)) на уровне productgroups на 15%
                    $AdwordsApiController->increaseCPC($campaignObject, 15);
                    // Во всех случаях
                    $this->changeCampaignPhase(2, '2-2-2-3');
                    return true;
                }
            }
        }
    }

    public function blockThree()
    {
        // Информация о компании для сравнения
        $campaign = $this->currentCmp;
        // Обьект компании для манипуляций
        $campaignObject = $this->objectCmp;
        // Статистика за эту неделю
        $thisWeekCmp = $this->thisWeekCmp;
        // Статистика за предыдущую неделю
        $lastWeekCmp = $this->lastWeekCmp;
        // Адвордс апи контроллер
        $AdwordsApiController = $this->AdwordsApiController;

        $this->createLog([
            'task_id' => $this->task_id,
            'customer_id' => $campaign['customerId'],
            'campaign_id' => $campaign['campaignId'],
            'type' => 'processed',
            'task' => 'Запуск фазы 3',
            'message' => ($campaign['customerFirstCheck']) ? 'Не проверялась' : 'Уже проверялась'
        ]);

        if ($campaign['roas'] >= $campaign['zRoas']) {
            if ($thisWeekCmp->cost >= $lastWeekCmp->cost) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    // Увеличиваем PrevBudget на 30% (проверка на не превышение MaxBudget)
                    $AdwordsApiController->increaseBudget($campaignObject, 30, $campaign['max_budget']);
                    // Во всех случаях
                    $this->changeCampaignPhase(3);
                    return true;
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    // >>> Увеличиваем Target ROAS для кампании на 25%.
                    // >>> Увеличиваем PrevBudget на 20% (проверка на не превышение MaxBudget)
                    if( $AdwordsApiController->increaseBudget($campaignObject, 20, $campaign['max_budget'])){
                        $AdwordsApiController->increaseTargetRoas($campaignObject, 25);
                    }
                    // Во всех случаях
                    $this->changeCampaignPhase(3);
                    return true;
                }
                elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    // Увеличиваем Target ROAS для кампании на 75%.
                    // Увеличиваем PrevBudget на 10% ( проверка на не превышение MaxBudget )
                    $AdwordsApiController->increaseTargetRoas($campaignObject, 75);
                    // Во всех случаях
                    $this->changeCampaignPhase(3);
                    return true;
                }
            }
            if ($thisWeekCmp->cost < $lastWeekCmp->cost) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    // Во всех случаях
                    $this->changeCampaignPhase(3);
                    return true;
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    // Уменьшаем Target ROAS для кампании на 25% (делаем проверку на MinTarget ROAS)
                    $AdwordsApiController->decreaseTargetRoas($campaignObject, 25);
                    // Во всех случаях
                    $this->changeCampaignPhase(3);
                    return true;
                }
                elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    // Уменьшаем Target ROAS для кампании на 50% (делаем проверку на MinTarget ROAS)
                    $AdwordsApiController->decreaseTargetRoas($campaignObject, 50);
                    // Во всех случаях
                    $this->changeCampaignPhase(3);
                    return true;
                }
            }
        }

        if ($campaign['roas'] < $campaign['zRoas']) {
            if ($thisWeekCmp->cost >= $lastWeekCmp->cost) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    // Увеличиваем Target ROAS для кампании на 100%.
                    // Уменьшаем PrevBudget на 10%
                    $AdwordsApiController->increaseTargetRoas($campaignObject, 100);
                    $AdwordsApiController->decreaseBudget($campaignObject, 10);
                    // Во всех случаях
                    $this->changeCampaignPhase(3);
                    return true;
                }
                if ($thisWeekCmp->clicks  > ($lastWeekCmp->clicks / 2)) {
                    // Выключаем Standard Shopping кампанию и включаем Smart Shopping кампанию со ставкой (Maximize Conversion Value - default)
                    if($AdwordsApiController->standartToSmart($campaignObject, $this->standartAndSmartList)){
                        $this->changeCampaignPhase(4);
                        return true;
                    }
                }
                elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    // Выключаем Standard Shopping кампанию и включаем Smart Shopping кампанию со ставкой (Maximize Conversion Value - default)
                    if($AdwordsApiController->standartToSmart($campaignObject, $this->standartAndSmartList)){
                        $this->changeCampaignPhase(4);
                        return true;
                    }
                }
            }
            if ($thisWeekCmp->cost < $lastWeekCmp->cost) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    // Выключаем Standard Shopping кампанию и включаем Smart Shopping кампанию со ставкой (Maximize Conversion Value - default)
                    if($AdwordsApiController->standartToSmart($campaignObject, $this->standartAndSmartList)){
                        $this->changeCampaignPhase(4);
                        return true;
                    }
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    //    Уменьшаем Target ROAS для кампании на 50%( делаем проверку на MinTarget ROAS )
                    if ( $AdwordsApiController->decreaseTargetRoas($campaignObject, 50)) {
                        if($AdwordsApiController->standartToSmart($campaignObject, $this->standartAndSmartList)){
                            $this->changeCampaignPhase(4);
                            return true;
                        }
                    }
                    $this->changeCampaignPhase('3');
                    return true;
                }
                elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    //    Уменьшаем Target ROAS для кампании на 100% ( делаем проверку на MinTarget ROAS )
                    if ( $AdwordsApiController->decreaseTargetRoas($campaignObject, 100)) {
                        if($AdwordsApiController->standartToSmart($campaignObject, $this->standartAndSmartList)){
                            $this->changeCampaignPhase(4);
                            return true;
                        }
                    }
                    $this->changeCampaignPhase(3);
                    return true;
                }
            }
        }
    }

    public function blockFourth()
    {
        // Информация о компании для сравнения
        $campaign = $this->currentCmp;
        // Обьект компании для манипуляций
        $campaignObject = $this->objectCmp;
        // Статистика за эту неделю
        $thisWeekCmp = $this->thisWeekCmp;
        // Статистика за предыдущую неделю
        $lastWeekCmp = $this->lastWeekCmp;
        // Адвордс апи контроллер
        $AdwordsApiController = $this->AdwordsApiController;

        $this->createLog([
            'task_id' => $this->task_id,
            'customer_id' => $campaign['customerId'],
            'campaign_id' => $campaign['campaignId'],
            'type' => 'processed',
            'task' => 'Запуск фазы 4',
            'message' => ($campaign['customerFirstCheck']) ? 'Не проверялась' : 'Уже проверялась'
        ]);

        // Если maximizeConvValueRoas = default
        $isDefault = (isset($campaign['maximizeConvValueRoasIsDefault'])) ? $campaign['maximizeConvValueRoasIsDefault'] : false;

        if ($campaign['roas'] >= $campaign['zRoas']) {
            if ($thisWeekCmp->cost >= $lastWeekCmp->cost) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    $AdwordsApiController->increaseBudget($campaignObject, 30, $campaign['max_budget']);
                    // В любом случае
                    $this->changeCampaignPhase(4, '4-1-1-[1]');
                    return true;
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    if($isDefault){
                        $AdwordsApiController->increaseBudget($campaignObject, 30, $campaign['max_budget']);
                        $this->changeCampaignPhase(4, '4-1-1-2-[default]');
                        return true;
                    }
                    else{
                        // Увеличиваем Maximize Conv. Value(ROAS) для кампании на 25%.
                        // Увеличиваем PrevBudget на 20% (проверка на не превышение MaxBudget)
                        $AdwordsApiController->increaseMaximizeConvValueRoas($campaignObject, 25);
                        $AdwordsApiController->increaseBudget($campaignObject, 20, $campaign['max_budget']);
                        $this->changeCampaignPhase(4, '4-1-1-2-[%]');
                        return true;
                    }
                    // В любом случае
                    $this->changeCampaignPhase(4);
                    return true;
                }
                elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    if($isDefault){
                        $AdwordsApiController->increaseBudget($campaignObject,  30);
                        $this->changeCampaignPhase(4, '4-1-1-3-[default]');
                        return true;
                    }
                    else{
                        // Увеличиваем Maximize Conv. Value(ROAS) для кампании на 75%.
                        // Увеличиваем PrevBudget на 10% (проверка на не превышение MaxBudget)
                        $AdwordsApiController->increaseMaximizeConvValueRoas($campaignObject, 75);
                        $AdwordsApiController->increaseBudget($campaignObject, 10, $campaign['max_budget']);
                        $this->changeCampaignPhase(4, '4-1-1-3-[%]');
                        return true;
                    }
                    // В любом случае
                    $this->changeCampaignPhase(4);
                    return true;
                }
            }
            if ($thisWeekCmp->cost < $lastWeekCmp->cost) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    // Оставляем как есть без оптимизации
                    $this->changeCampaignPhase(4, '4-1-2-1-[end]');
                    return true;
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    if($isDefault){
                        // Оставляем как есть без оптимизации
                        $this->changeCampaignPhase(4,'4-1-2-2-[default]');
                        return true;
                    }
                    else{
                        // Увеличиваем Maximize Conv. Value(ROAS) для кампании на 25%.
                        // Увеличиваем PrevBudget на 20% (проверка на не превышение MaxBudget)
                        $AdwordsApiController->decreaseMaximizeConvValueRoas($campaignObject, 25, true);
                        $this->changeCampaignPhase(4, '4-1-2-2-[%]');
                        return true;
                    }

                }
                elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    if($isDefault){
                        // Оставляем как есть без оптимизации
                        $this->changeCampaignPhase(4, '4-1-2-3-[default]');
                        return true;
                    }
                    else{
                        // Увеличиваем Maximize Conv. Value(ROAS) для кампании на 50%.
                        // Увеличиваем PrevBudget на 20% (проверка на не превышение MaxBudget)
                        $AdwordsApiController->decreaseMaximizeConvValueRoas($campaignObject, 50, true);
                        $this->changeCampaignPhase(4, '4-1-2-3-[%]');
                        return true;
                    }
                }
            }
        }

        if ($campaign['roas'] < $campaign['zRoas']) {
            if ($thisWeekCmp->cost >= $lastWeekCmp->cost) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks) {
                    if($isDefault){
                        // Ставим ставку Maximize Conv. Value(ROAS) для кампании 500%.
                        $AdwordsApiController->setMaximizeConvValueRoas($campaignObject, 500);
                        $this->changeCampaignPhase(4, '4-2-1-1-[default]');
                        return true;
                    }
                    else{
                        // Увеличиваем Maximize Conv. Value(ROAS) для кампании на 100%. Уменьшаем  PrevBudget  на 10%
                        $AdwordsApiController->increaseMaximizeConvValueRoas($campaignObject, 100);
                        $AdwordsApiController->decreaseBudget($campaignObject, 10);
                        $this->changeCampaignPhase(4, '4-2-1-1-[%]');
                        return true;
                    }
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    if($isDefault){
                        // Ставим ставку Maximize Conv. Value(ROAS) для кампании 500%.
                        $AdwordsApiController->setMaximizeConvValueRoas($campaignObject, 500);
                        $this->changeCampaignPhase(4, '4-2-1-2-[default]');
                        return true;
                    }
                    else{
                        // Shopping to standart
                        $AdwordsApiController->smartToStandart($campaignObject, $this->standartAndSmartList);
                        $this->changeCampaignPhase(3, '4-2-1-2-[%][change campaign]');
                        return true;
                    }
                }
                elseif ($thisWeekCmp->clicks <= ($lastWeekCmp->clicks / 2)) {
                    if($isDefault){
                        // Ставим ставку Maximize Conv. Value(ROAS) для кампании 500%.
                        $AdwordsApiController->setMaximizeConvValueRoas($campaignObject, 500);
                        $this->changeCampaignPhase(4, '4-2-1-3-[default]');
                        return true;
                    }
                    else{
                        // Shopping to standart
                        $AdwordsApiController->smartToStandart($campaignObject, $this->standartAndSmartList);
                        $this->changeCampaignPhase(3, '4-2-1-3-[%][change campaign]');
                        return true;
                    }
                }
            }
            if ($thisWeekCmp->cost < $lastWeekCmp->cost) {
                if ($thisWeekCmp->clicks >= $lastWeekCmp->clicks ) {
                    // %
                    if(!$isDefault){
                        $AdwordsApiController->setMaximizeConvValueRoas($campaignObject, 'default');
                        $this->changeCampaignPhase(4, '4-2-2-1-[%]');
                        return true;
                    }
                    // default
                    else{
                       $AdwordsApiController->smartToStandart($campaignObject, $this->standartAndSmartList);
                       $this->changeCampaignPhase(32, '4-2-2-1-[default][change campaign]');
                       return true;
                    }
                }
                if ($thisWeekCmp->clicks > ($lastWeekCmp->clicks / 2)) {
                    if(!$isDefault){
                        // Уменьшаем Maximize Conv. Value(ROAS) для кампании на 50% (делаем проверку на  MinMaximize Conv. Value(ROAS))
                        $AdwordsApiController->decreaseMaximizeConvValueRoas($campaignObject, 50, true, true);
                        $this->changeCampaignPhase(4, '4-2-2-2-[%]');
                        return true;
                    }
                    // default
                    else{
                        $AdwordsApiController->smartToStandart($campaignObject, $this->standartAndSmartList);
                        $this->changeCampaignPhase(32, '4-2-2-2-[default]');
                        return true;
                    }
                }
                elseif ($thisWeekCmp->clicks  <= ($lastWeekCmp->clicks  / 2)) {
                    if(!$isDefault){
                        // Уменьшаем Maximize Conv. Value(ROAS) для кампании на 100% (делаем проверку на  MinMaximize Conv. Value(ROAS))
                        $AdwordsApiController->decreaseMaximizeConvValueRoas($campaignObject, 100, true, true);
                        $this->changeCampaignPhase(4, '4-2-2-3-[%]');
                        return true;
                    }
                    // default
                    else{
                        $AdwordsApiController->smartToStandart($campaignObject, $this->standartAndSmartList);
                        $this->changeCampaignPhase(32, '4-2-2-3-[default]');
                        return true;
                    }
                }
            }
        }
    }

    /**
     * @param string $phase
     * @return bool
     */
    public function changeCampaignPhase($phase = false, $patch = ''){

        // Информация о компании для сравнения
        $campaign = $this->currentCmp;
        if(!$phase) $phase = 'not set!';

        $this->createLog([
            'task_id' => $this->task_id,
            'customer_id' => $campaign['customerId'],
            'campaign_id' => $campaign['campaignId'],
            'type' => 'processed',
            'task' => 'Завершено!',
            'message' => 'Следующий блок : '.$phase. ' Путь : '.$patch
        ]);

        // Устанавливаем дату последней проверки

        try {
            $customer = Customer::where('customer_id', '=', $campaign['customerId'])->get()->first();
            $nextDate = Carbon::now()->addDays(7)->format('Ymd');
            $customer->last_check_date = $nextDate;
            $customer->save();
        } catch (\Throwable $e) {
            /* logs */
            $this->createLog([
                'task_id' => $this->task_id,
                'customer_id' => $campaign['customerId'],
                'type' => 'error',
                'task' => 'Установка новой даты проверки',
                'message' => $e->getMessage().' '.$e->getLine()
            ]);
            return false;
        }

        return true;
    }

    // Create campaigns
    public function createCampaigns($customerId){

        // Адвордс апи контроллер
        $AdwordsApiController = $this->AdwordsApiController;

        $this->createLog([
            'task_id' => $this->task_id,
            'customer_id' => $customerId,
            'campaign_id' => '',
            'type' => 'processed',
            'task' => 'Создание компаний для '.$customerId,
            'message' => ''
        ]);

        $AdwordsApiController->createCampaign($customerId);

    }

    /*
     * D A I L Y   C H E C K E R
     */

    public function daily_checker_start()
    {

        /*
        Проверка shopping кампаний на исправность (Работает ли реклама)
        Проверка данных на (Clicks, Impressions, Search impres. share,
        CTR, CPC, Conversions, Conv. Rate, Conv. Value) и оптимизация
        кампаний проводится раз в 7 дней, с ретроспективой за последние 7
        дней. Ежедневная проверка
        */

        // 1 Получаем список активных customers

        // 2 Делаем запрос в апи на получение списка компаний по каждому customers ( только shopping )

        // 3 Проверяем значение Impressions

        /*
         * Обновляем порядковый номер проверки
         */
        $taskId = DailyTaskLogs::max('task_id');
        $taskId = (is_null($taskId)) ? 1 : ++$taskId;

        $this->createDailyLog([
            'task_id' => $taskId,
            'type' => 'processed',
            'message' => 'Запуск проверки'
        ]);

        // Получаем список customers
        $customers = $this->getCustomers();

        // Получаем список компаний для каждого customers по отдельности
        // Перебераем всех customers
        if ($customers->isEmpty()) {
            /* logs */
            // Если список аккаунтов для проверки пуст, выходим из метода
            $this->createDailyLog([
                'task_id' => $taskId,
                'type' => 'warning',
                'message' => 'Список customers пуст.'
            ]);
            return false;
        }
        /* logs */
        $this->createDailyLog([
            'task_id' => $taskId,
            'type' => 'processed',
            'message' => 'Количество найденых Customers : ' . count($customers)
        ]);

        $customers->each(function ($customer) use ($taskId) {
            // Получение только shopping компаний
            try {
                $settings = $customer->getRelation('account')->getDecryptAll();
                $AdwordsApiController = new AdwordsApiController($settings);
                $AdwordsApiController->setClientCustomerId($customer->customer_id);

                // Получаем по условию только enabled компании!
                $campaigns = $AdwordsApiController->getShoppingCampaigns(true);

                if($campaigns->isEmpty()){
                    /* logs */
                    $this->createDailyLog([
                        'task_id' => $taskId,
                        'customer_id' => $customer->customer_id,
                        'type' => 'warning',
                        'message' => 'Компаний типа shopping у этого аккаунта нет!'
                    ]);
                    return true;
                }

                // Получили список компаний. Теперь получаем статистику ( impressions )
                $campaigns->each(function ($campaign) use ($taskId, $AdwordsApiController, $customer){

                    /* logs */
                    $this->createDailyLog([
                        'task_id' => $taskId,
                        'customer_id' => $customer->customer_id,
                        'campaign_id' => $campaign->getId(),
                        'type' => 'processed',
                        'message' => 'Проверяем компанию!'
                    ]);

                    $campaignId = $campaign->getId();
                    $thisWeek = $AdwordsApiController->getPerfomanceReportThisWeek($campaignId)->result[0];

                    $impressions = $thisWeek->impressions;

                    if($impressions == 0){
                        /* logs */
                        $this->createDailyLog([
                            'task_id' => $taskId,
                            'customer_id' => $customer->customer_id,
                            'campaign_id' => $campaign->getId(),
                            'type' => 'warning',
                            'message' => 'Impressions = 0 !'
                        ]);

                    }else{
                        $this->createDailyLog([
                            'task_id' => $taskId,
                            'customer_id' => $customer->customer_id,
                            'campaign_id' => $campaign->getId(),
                            'type' => 'success',
                            'message' => 'Всё нормально! Impressions > 0 ! : '.$impressions
                        ]);
                    }
                });

            } catch (\Throwable $e) {
                /* logs */
                $this->createDailyLog([
                    'task_id' => $taskId,
                    'customer_id' => $customer->customer_id,
                    'type' => 'error',
                    'message' => $e->getMessage().' '.$e->getLine()
                ]);
                return true;
            }
        });
    }

}


