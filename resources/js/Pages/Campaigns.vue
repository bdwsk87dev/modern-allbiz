<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Кампании
      </h2>
    </template>


    <!-- Edit link campaign modal -->
    <div
        class="modal modal-link-edit opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
      <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
      <!--        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">-->
      <div class="modal-container bg-white w-6/12 mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div
            class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
          <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
               viewBox="0 0 18 18">
            <path
                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
          </svg>
          <span class="text-sm">(Esc)</span>
        </div>
        <!-- Add margin if you want to see some of the overlay behind the modal-->
        <div class="modal-content py-4 text-left px-6">
          <!--Title-->
          <div class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Привязать кампанию</p>
            <div class="modal-close cursor-pointer z-50">
              <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                   height="18" viewBox="0 0 18 18">
                <path
                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
              </svg>
            </div>
          </div>
          <!--Body-->
          <p><b>Текущая кампания : </b><br>{{ coldata.campaign_name }}</p><br>
          <select multiple v-model='new_pair_id'
                  class="w-full h-48 hover:text-blue-500 sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-2 border border-gray-400 rounded  focus:outline-none text-xs sm:text-sm">
            <optgroup label="Smart кампании">
              <option v-if="pair.is_smart" class="text-black" v-for="pair in this.suitablePairs"
                      v-bind:value="pair.idCampaign">
                {{ pair.campaign_name }} {{ pair.campaign_id }} {{ pair.customer_name }}
              </option>
            </optgroup>
            <optgroup label="Standart кампании">
              <option v-if="!pair.is_smart" class="text-black" v-for="pair in this.suitablePairs"
                      v-bind:value="pair.idCampaign">
                {{ pair.campaign_name }} {{ pair.campaign_id }} {{ pair.customer_name }}
              </option>
            </optgroup>
          </select>
          <div class="flex justify-end pt-2">
            <button @click="setpair(true)"
                    class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">
              Удалить привязку
            </button>
            <button @click="setpair()"
                    class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">
              Привязать
            </button>
            <button class="modal-close px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
              Отмена
            </button>
          </div>
        </div>
      </div>
    </div>


    <!-- Change status modal -->
    <div
        class="modal modal-change-status opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
      <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
      <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div
            class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
          <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
               viewBox="0 0 18 18">
            <path
                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
          </svg>
          <span class="text-sm">(Esc)</span>
        </div>
        <!-- Add margin if you want to see some of the overlay behind the modal-->
        <div class="modal-content py-4 text-left px-6">
          <!--Title-->
          <div class="flex justify-between items-center pb-3">

            <p class="text-2xl font-bold">Изменить статус кампании</p>



            <div class="modal-close cursor-pointer z-50">
              <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                   height="18" viewBox="0 0 18 18">
                <path
                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
              </svg>
            </div>
          </div>

          <div>


            <p>Что бы изменения вступили в силу, проверьте что галочка "Тестовый режим" не установлена!</p>


            <br>


            <p>Текущий статус в системе : </p>
            <select v-model="campaignSatusModal.currentStatus">
              <option >ENABLED</option>
              <option>PAUSED</option>
            </select>
            <br>
            <br>
            <p>Текущий статус в adwords : </p>
            {{ campaignSatusModal.adwStatus }}
            <br><br>
          </div>

          <button @click="saveStatus()" class="px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
            Сохранить
          </button>

          <button  @click="adwGetStatus()" class="px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
            Получить статус из Adwords
          </button>

<!--          <button class="modal-close px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">-->
<!--            Отмена-->
<!--          </button>-->
        </div>
      </div>
    </div>

    <!-- Create campaign modal -->
    <div
        class="modal modal-create opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
      <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
      <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div
            class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
          <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
               viewBox="0 0 18 18">
            <path
                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
          </svg>
          <span class="text-sm">(Esc)</span>
        </div>
        <!-- Add margin if you want to see some of the overlay behind the modal-->
        <div class="modal-content py-4 text-left px-6">
          <!--Title-->
          <div class="flex justify-between items-center pb-3">
            <p v-if="!actionItemId" class="text-2xl font-bold">Создание новой компании</p>
            <p v-else class="text-2xl font-bold">Редактирование компании</p>
            <div class="modal-close cursor-pointer z-50">
              <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                   height="18" viewBox="0 0 18 18">
                <path
                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
              </svg>
            </div>
          </div>





          <!--Body-->
          Внимание! Пока не активно!<br><br>

          <p>Название компании</p>
          <input v-model="createData.campaign_name" type="text" placeholder="Введите название"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">
          <p>Начальный бюжет</p>
          <input v-model="createData.start_budget" type="text" placeholder="Начальный бюджет"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">
          <p>Максимальный бюжет</p>
          <input v-model="createData.max_budget" type="text" placeholder="Максимальный бюджет"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">
          <!--Footer-->
          <div class="flex justify-end pt-2">
            <!--                      <button v-if="!actionItemId" class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">-->
            <!--                          Создать-->
            <!--                      </button>-->
            <!--                      <button v-else @click="update()" class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">-->
            <!--                          Сохранить-->
            <!--                      </button>-->
            <button class="modal-close px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
              Отмена
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete confirm modal -->
    <div
        class="modal modal-delete opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
      <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
      <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div
            class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
          <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
               viewBox="0 0 18 18">
            <path
                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
          </svg>
          <span class="text-sm">(Esc)</span>
        </div>

        <div class="modal-content py-4 text-left px-6">
          <!--Title-->
          <div class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Удаление компании</p>
            <div class="modal-close cursor-pointer z-50">
              <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                   height="18" viewBox="0 0 18 18">
                <path
                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
              </svg>
            </div>
          </div>
          <!-- Content -->
          <p>Вы действительно хотите удадалить эту компанию ?</p>

          <!--Footer-->
          <div class="flex justify-end pt-2">
            <button @click="deleteItem()"
                    class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">
              Да
            </button>
            <button class="modal-close px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
              Нет
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Import campaigns -->
    <div
        class="modal modal-import opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
      <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
      <div class="modal-container bg-white w-6/12 h-12/12  rounded shadow-lg z-50 overflow-y-auto">
        <div
            class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
          <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
               viewBox="0 0 18 18">
            <path
                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
          </svg>
          <span class="text-sm">(Esc)</span>
        </div>

        <div class="modal-content py-4 text-left px-6">
          <!--Title-->
          <div class="flex justify-between items-center pb-3">
            <p class="text-2xl font-bold">Импортирование кампаний клиента.</p>
            <div class="modal-close cursor-pointer z-50">
              <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                   height="18" viewBox="0 0 18 18">
                <path
                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
              </svg>
            </div>
          </div>
          <!-- Content -->
          <p>По умолчанию в списке отображаются последние 100 клиентов,</p>
          <p>которые были импортированны в систему. Если клиента в списке нет, используйте поиск.</p>

          <p class="mt-2">Поиск клиента :</p>

          <input v-model="customersFilter" @change="getCustomersForSelect()" type="text" placeholder="Поиск..."
                 class="w-6/12 mt-2 hover:text-blue-500 hidden sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-4 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

          <button class="mt-2 px-1 bg-indigo-500 p-1 rounded-lg text-white hover:bg-indigo-400">Найти</button>

          <p class="mt-2">Список клиентов :</p>

          <select multiple v-model='customers_ids'
                  class="mt-2 w-full h-64 hover:text-blue-500 sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-2 border border-gray-400 rounded  focus:outline-none text-xs sm:text-sm">
            <option class="text-black" v-for="customer in this.customers" v-bind:value="customer.id">
              {{ customer.customer_name }} [{{ customer.customer_id }}]
            </option>
          </select>

          <br>

          <!--Footer-->
          <div class="flex justify-end pt-2">
            <button @click="importCampaigns()"
                    class="modal-close px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">
              Импорт
            </button>
            <button class="modal-close px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
              Отмена
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="py-6">

      <div class="max-w-9xl mx-auto sm:px-6 lg:px-8 ">
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200 mb-2 rounded">
          <div class="text-gray-500 text-xs">Для импорта кампаний нажмите на кнопку "Импортировать кампании" и выберете клиента, кампании которого вы хотите импортировать.<br>
            Для правильной работы алгоритма нужно связать кампании между собой по такому принципу : одна смарт кампания = одна стандарт кампания.<br>
            Нажмите на шестерню напротив кампании, и укажите другую кампанию для привязки. Внимание! В списке для привязки отображаются только кампании одного клиента ( customer )<br>
            Вы можете приостановить или включить кампании на стороне adwords, кликнув по иконкам в колонке "Смарт. комп."
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-4 pb-4">
          <ul class="grid grid-cols-1">

            <li class="col-start-1 col-end-1 cursor-pointer select-none self-center hover:black flex">
              <button
                  @click="toggleModal('create')"
                  class="ml-8 hover:text-blue-500 bg-transparent hover:bg-secondary text-gray-600 py-2 hove:bg-gray-800 hover:text-white px-4 border border-gray-400 rounded ml-2 focus:outline-none text-xs">
                Импортировать компании
              </button>
              <button
                  @click="toggleModal('modal-create')"
                  class="ml-4 hover:text-blue-500 bg-transparent hover:bg-secondary text-gray-600 py-2 hove:bg-gray-800 hover:text-white px-4 border border-gray-400 rounded ml-2 focus:outline-none text-xs">
                Создать компанию
              </button>

              <select v-model="prefType" class="ml-4 pl-2 pr-2 rounded text-xs border border-gray-400 bg-transparent text-gray-600 cursor-pointer">
                <option selected value="1">Статистика за 7 дней</option>
                <option value="2">Статистика за 30 дней</option>
              </select>

            </li>
            <li class="justify-self-end col-start-2 col-end-5 cursor-pointer select-none self-center pr-8">
              <svg @click="getCampaigns()" width="18" height="18"
                   class="absolute ml-48 mt-2.5 pl-2 lg:w-auto"
                   viewBox="0 0 18 18"
                   fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M8.11086 15.2217C12.0381 15.2217 15.2217 12.0381 15.2217 8.11086C15.2217 4.18364 12.0381 1 8.11086 1C4.18364 1 1 4.18364 1 8.11086C1 12.0381 4.18364 15.2217 8.11086 15.2217Z"
                    stroke="#839fcc" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.9993 16.9993L13.1328 13.1328" stroke="#839fcc" stroke-linecap="round"
                      stroke-linejoin="round"/>
              </svg>
              <input @change="getCampaigns()" type="text" v-model="searchString" placeholder="Поиск"
                     class="hover:text-blue-500 hidden sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-4 border border-gray-400 rounded ml-2 sm:ml-4 focus:outline-none text-xs sm:text-sm">
            </li>
          </ul>

          <div class="align-middle inline-block min-w-full overflow-hidden bg-white shadow-dashboard px-8 pt-3">
            <!-- Pagination -->
            <div class="sm:flex-1 sm:flex sm:items-center sm:justify-between work-sans">
              <div>
                <pagination :limit="2" :data="vuePagination" @pagination-change-page="changePage">
                  <span slot="prev-nav">&lt; Назад</span>-->
                  <span slot="next-nav">Вперёд &gt;</span>
                </pagination>
              </div>
            </div>
            <table class="min-w-full">
              <thead>
              <tr>
                <th @click="changeSort('campaigns.id')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Id</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('campaigns.id') }}</div>
                  </div>
                </th>
                <th @click="changeSort('campaigns.campaign_name')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Название</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('campaigns.campaign_name') }}</div>
                  </div>
                </th>
                <th @click="changeSort('campaigns.campaign_id')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Id кампании (adw)</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('campaigns.campaign_id') }}</div>
                  </div>
                </th>
                <th @click="changeSort('customers.customer_name')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider w-36">
                  <div class="inline-flex">
                    <div class="inline-block">Имя клиента</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('customers.customer_name') }}
                    </div>
                  </div>
                </th>
                <th @click="changeSort('campaigns.phase')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">След. блок</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('campaigns.phase') }}</div>
                  </div>
                </th>
                <th @click="changeSort('campaigns.last_check_date')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Дата посл. проверки</div>
                    <div class="inline-block min-w-16px">{{
                        sortIconShow('campaigns.last_check_date')
                      }}
                    </div>
                  </div>
                </th>


                <th @click="changeSort('campaigns.start_budget')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Cost</div>
                    <div class="inline-block min-w-16px">{{
                        sortIconShow('campaigns.start_budget')
                      }}
                    </div>
                  </div>
                </th>

                <th @click="changeSort('campaigns.start_budget')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Conv</div>
                    <div class="inline-block min-w-16px">{{
                        sortIconShow('campaigns.start_budget')
                      }}
                    </div>
                  </div>
                </th>

                <th @click="changeSort('campaigns.start_budget')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Conv value</div>
                    <div class="inline-block min-w-16px">{{
                        sortIconShow('campaigns.start_budget')
                      }}
                    </div>
                  </div>
                </th>

                <th @click="changeSort('campaigns.start_budget')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Смарт комп.</div>
                    <div class="inline-block min-w-16px">{{
                        sortIconShow('campaigns.start_budget')
                      }}
                    </div>
                  </div>
                </th>


                <th @click="changeSort('campaigns.status')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Статус</div>
                    <div class="inline-block min-w-16px">{{
                        sortIconShow('campaigns.status')
                      }}
                    </div>
                  </div>
                </th>


                <th @click="changeSort('campaigns.start_budget')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider w-6">
                  <div class="inline-flex">
                    <div class="inline-block">Линк</div>
                    <div class="inline-block min-w-16px">{{
                        sortIconShow('campaigns.start_budget')
                      }}
                    </div>
                  </div>
                </th>

                <th @click="changeSort('campaigns.active')"
                    class="px-0 cursor-pointer py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider w-6">
                  <div class="inline-flex">
                    <div class="inline-block">Aкт.</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('campaigns.active') }}
                    </div>
                  </div>
                </th>
                <th class="px-0 py-3 border-b-2 border-blue-500  text-left text-sm leading-4 text-blue-500 tracking-wider w-10">
                  Настройки
                </th>
              </tr>



              </thead>
              <tbody class="bg-white" name="result-table" is="transition-group">
              <tr class="result-table-item" v-for="col of campaigns" v-bind:key="col.idCampaign">
                <td class=" px-3 py-4 border-b border-sky-900 text-blue-900  text-sm leading-5">
                  {{ col.idCampaign }}
                </td>
                <td class="px-3 py-4 border-b border-sky-900 text-blue-900 text-sm leading-5 break-normal">
                  {{ col.campaign_name }}
                </td>
                <td class="px-3 py-4  border-b border-sky-900 text-blue-900  text-sm leading-5 break-normal">
                  {{ col.campaign_id }}
                </td>
                <td class="px-3 py-4  border-b text-blue-900 text-sm leading-5 break-normal">
                  {{ col.customer_name }}
                </td>
                <td class="px-3 py-4  border-b border-sky-900 text-blue-900 text-sm leading-5 break-normal">
                  <p v-if="col.phase == 0">Новая компания</p>
                  <p v-else>Блок {{ col.phase }}</p>
                </td>
                <td v-if="col.last_check_date"
                    class="px-3 py-4  border-b border-sky-900 text-blue-900 text-sm leading-5 break-normal">
                  {{ col.last_check_date }}
                </td>
                <td v-else="col.last_check_date"
                    class="px-3 py-4  border-b border-sky-900 text-blue-900  text-sm leading-5 break-normal">
                  Проверок не было
                </td>

                <td v-if="prefType==1" class="px-3 py-4  border-b border-sky-900 text-blue-900  text-sm leading-5 break-normal w-24">
                  {{ col.cost }}
                </td>
                <td v-else class="px-3 py-4  border-b border-sky-900 text-blue-900  text-sm leading-5 break-normal w-24">
                  {{ col.cost_30 }}
                </td>

                <td v-if="prefType==1" class="px-3 py-4  border-b border-sky-900 text-blue-900  text-sm leading-5 break-normal w-24">
                  {{ col.conv }}
                </td>
                <td v-else class="px-3 py-4  border-b border-sky-900 text-blue-900  text-sm leading-5 break-normal w-24">
                 {{ col.conv_30 }}
                </td>


                <td v-if="prefType==1" class="px-3 py-4  border-b border-sky-900 text-blue-900 text-sm leading-5 break-normal w-24">
                  {{ col.conv_value }}
                </td>

                <td v-else class="px-3 py-4  border-b border-sky-900 text-blue-900 text-sm leading-5 break-normal w-24">
                 {{ col.conv_value_30 }}
                </td>



                <td class="px-3 py-4  border-b border-sky-900 text-blue-900  text-sm leading-5 w-6">
                                    <span v-if="col.is_smart"
                                          class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                      <span aria-hidden
                                            class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                      <span class="relative text-xs">Smart</span>
                                    </span>
                  <span v-else="col.is_smart"
                        class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                      <span aria-hidden
                                            class="absolute inset-0 bg-yellow-200 opacity-50 rounded-full"></span>
                                      <span class="relative text-xs">Standart</span>
                                    </span>
                </td>




                <td class="px-3 py-4 border-b border-sky-900 border-b text-blue-900 text-sm leading-5 break-normal w-6">
                                  <span @click="toggleModal('change-status', col)" v-if="col.status == 'ENABLED'"
                                        class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                      <span aria-hidden
                                            class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                      <span class="relative text-xs">Enabled</span>
                                  </span>
                  <span @click="toggleModal('change-status', col)" v-else-if="col.status == 'PAUSED'"
                        class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                      <span aria-hidden
                                            class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                      <span class="relative text-xs">Paused</span>
                                  </span>
                  <span v-else
                        class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                      <span aria-hidden
                                            class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                      <span class="relative text-xs">{{ col.status }}</span>
                                  </span>
                </td>





                <td class="px-3 py-4  border-b border-sky-900 text-blue-900  text-sm leading-5 w-12">
                                    <span
                                        @click="toggleModal('edit-campaign-link', col); actionItemId = col.idCampaign;"
                                        v-if="col.link_campaign"
                                        class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                        <span aria-hidden
                                              class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cogs"
                                             role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                                             class="icon alt svg-inline--fa fa-cogs fa-w-20"><path fill="currentColor"
                                                                                                   d="M512.1 191l-8.2 14.3c-3 5.3-9.4 7.5-15.1 5.4-11.8-4.4-22.6-10.7-32.1-18.6-4.6-3.8-5.8-10.5-2.8-15.7l8.2-14.3c-6.9-8-12.3-17.3-15.9-27.4h-16.5c-6 0-11.2-4.3-12.2-10.3-2-12-2.1-24.6 0-37.1 1-6 6.2-10.4 12.2-10.4h16.5c3.6-10.1 9-19.4 15.9-27.4l-8.2-14.3c-3-5.2-1.9-11.9 2.8-15.7 9.5-7.9 20.4-14.2 32.1-18.6 5.7-2.1 12.1.1 15.1 5.4l8.2 14.3c10.5-1.9 21.2-1.9 31.7 0L552 6.3c3-5.3 9.4-7.5 15.1-5.4 11.8 4.4 22.6 10.7 32.1 18.6 4.6 3.8 5.8 10.5 2.8 15.7l-8.2 14.3c6.9 8 12.3 17.3 15.9 27.4h16.5c6 0 11.2 4.3 12.2 10.3 2 12 2.1 24.6 0 37.1-1 6-6.2 10.4-12.2 10.4h-16.5c-3.6 10.1-9 19.4-15.9 27.4l8.2 14.3c3 5.2 1.9 11.9-2.8 15.7-9.5 7.9-20.4 14.2-32.1 18.6-5.7 2.1-12.1-.1-15.1-5.4l-8.2-14.3c-10.4 1.9-21.2 1.9-31.7 0zm-10.5-58.8c38.5 29.6 82.4-14.3 52.8-52.8-38.5-29.7-82.4 14.3-52.8 52.8zM386.3 286.1l33.7 16.8c10.1 5.8 14.5 18.1 10.5 29.1-8.9 24.2-26.4 46.4-42.6 65.8-7.4 8.9-20.2 11.1-30.3 5.3l-29.1-16.8c-16 13.7-34.6 24.6-54.9 31.7v33.6c0 11.6-8.3 21.6-19.7 23.6-24.6 4.2-50.4 4.4-75.9 0-11.5-2-20-11.9-20-23.6V418c-20.3-7.2-38.9-18-54.9-31.7L74 403c-10 5.8-22.9 3.6-30.3-5.3-16.2-19.4-33.3-41.6-42.2-65.7-4-10.9.4-23.2 10.5-29.1l33.3-16.8c-3.9-20.9-3.9-42.4 0-63.4L12 205.8c-10.1-5.8-14.6-18.1-10.5-29 8.9-24.2 26-46.4 42.2-65.8 7.4-8.9 20.2-11.1 30.3-5.3l29.1 16.8c16-13.7 34.6-24.6 54.9-31.7V57.1c0-11.5 8.2-21.5 19.6-23.5 24.6-4.2 50.5-4.4 76-.1 11.5 2 20 11.9 20 23.6v33.6c20.3 7.2 38.9 18 54.9 31.7l29.1-16.8c10-5.8 22.9-3.6 30.3 5.3 16.2 19.4 33.2 41.6 42.1 65.8 4 10.9.1 23.2-10 29.1l-33.7 16.8c3.9 21 3.9 42.5 0 63.5zm-117.6 21.1c59.2-77-28.7-164.9-105.7-105.7-59.2 77 28.7 164.9 105.7 105.7zm243.4 182.7l-8.2 14.3c-3 5.3-9.4 7.5-15.1 5.4-11.8-4.4-22.6-10.7-32.1-18.6-4.6-3.8-5.8-10.5-2.8-15.7l8.2-14.3c-6.9-8-12.3-17.3-15.9-27.4h-16.5c-6 0-11.2-4.3-12.2-10.3-2-12-2.1-24.6 0-37.1 1-6 6.2-10.4 12.2-10.4h16.5c3.6-10.1 9-19.4 15.9-27.4l-8.2-14.3c-3-5.2-1.9-11.9 2.8-15.7 9.5-7.9 20.4-14.2 32.1-18.6 5.7-2.1 12.1.1 15.1 5.4l8.2 14.3c10.5-1.9 21.2-1.9 31.7 0l8.2-14.3c3-5.3 9.4-7.5 15.1-5.4 11.8 4.4 22.6 10.7 32.1 18.6 4.6 3.8 5.8 10.5 2.8 15.7l-8.2 14.3c6.9 8 12.3 17.3 15.9 27.4h16.5c6 0 11.2 4.3 12.2 10.3 2 12 2.1 24.6 0 37.1-1 6-6.2 10.4-12.2 10.4h-16.5c-3.6 10.1-9 19.4-15.9 27.4l8.2 14.3c3 5.2 1.9 11.9-2.8 15.7-9.5 7.9-20.4 14.2-32.1 18.6-5.7 2.1-12.1-.1-15.1-5.4l-8.2-14.3c-10.4 1.9-21.2 1.9-31.7 0zM501.6 431c38.5 29.6 82.4-14.3 52.8-52.8-38.5-29.6-82.4 14.3-52.8 52.8z"
                                                                                                   class=""></path></svg>
                                    </span>
                  <span @click="toggleModal('edit-campaign-link', col); actionItemId = col.idCampaign;" v-else
                        class="relative inline-block px-3 py-1 font-semibold  text-red-500 leading-tight cursor-pointer">
                                        <span aria-hidden class="absolute inset-0  opacity-50 rounded-full"></span>
                                       <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cog"
                                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                            class="svg-inline--fa fa-cog fa-w-12 fa-2x"><path fill="currentColor"
                                                                                              d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"
                                                                                              class=""></path></svg>  </span>
                </td>

                <td class="px-2.5 whitespace-no-wrap border-b border-sky-900 text-blue-900 text-sm leading-5">
                  <input
                      :checked="col.active"
                      @click="changeActive($event, col.idCampaign)"
                      type="checkbox"
                      class="form-checkbox cursor-pointer">
                </td>
                <td class="px-0 py-4 whitespace-no-wrap text-right border-b border-sky-900 text-sm leading-5">
                  <button @click="toggleModal('edit', col)"
                          class="px-3 py-2  text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none">
                    <font-awesome-icon icon="cogs" class="icon alt"/>
                  </button>
                  <button @click="toggleModal('delete'); actionItemId = col.idCampaign;"
                          class="px-3 py-2  text-red-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none">
                    <font-awesome-icon icon="user-times" class="icon alt"/>
                  </button>
                </td>
              </tr>
              </tbody>
            </table>
            <div class="sm:flex-1 sm:flex sm:items-center sm:justify-between mt-4 work-sans">
              <div>
                <pagination :limit="2" :data="vuePagination" @pagination-change-page="changePage">
                  <span slot="prev-nav">&lt; Назад</span>-->
                  <span slot="next-nav">Вперёд &gt;</span>
                </pagination>
              </div>
            </div>
            <p class="mt-5 text-xs">Всего записей : {{ pagination.total_page }}</p>
            <p class="text-xs">Отображаются : с {{ pagination.from ? pagination.from : 0 }} по
              {{ pagination.to ? pagination.to : 0 }}</p>
          </div>
        </div>
      </div>
    </div>


  </app-layout>
</template>

<script>
import AppLayout from '../Layouts/AppLayout'

export default {
  data() {
    return {
      // Campaigns list
      campaigns: [],
      // Campaigns list for import
      customers: [],
      // Search customer for import campaigns
      customersFilter: '',
      // user search string
      searchString: '',
      // sort icon images / asc / des
      sortIcon: '',
      // sort data
      sort: {
        'sortField': 'campaigns.id',
        'type': false
      },
      vuePagination: {},
      pagination: {
        'total_page': 0,
        'from': 0,
        'to': 0,
        'current_page': 1
      },
      createData: {
        'campaign_name': '',
        'start_budget': '',
        'max_budget': '',
      },
      coldata: {}, // for link edit
      editData: {},
      customers_ids: [],
      customerId: '',
      actionItemId: false,
      importStatus: {
        newCustomers: 0,
        issetCustomers: 0
      },
      suitablePairs: [],
      new_pair_id: [],

      campaignSatusModal: {
        currentCampaignId: '',
        currentStatus: 'Не получен',
        adwStatus: 'Не получен'
      },
      prefType: 1
    }
  },
  components: {
    AppLayout
  },
  methods: {
    getCampaigns(page = 1) {
      var app = this;
      axios({
        method: 'post',
        url: '/api/v1/campaigns/list',
        data: {
          'sortField': this.sort.sortField,
          'type': this.sort.type,
          'searchString': this.searchString,
          'page': page
        }
      }).then(function (resp) {
        // account data
        app.campaigns = resp.data.data;
        // data fo laravel-vue-pagination
        app.vuePagination = resp.data;
        // data for simple text pagination
        app.pagination.total_page = resp.data.total;
        app.pagination.from = resp.data.from;
        app.pagination.to = resp.data.to;
      }).catch(function (resp) {
        console.log(resp);
        alert("Could not load campaigns !");
      });
    },
    update() {
      axios({
        method: 'put',
        url: '/api/v1/campaigns/update' + this.actionItemId,
        data: {
          campaign_data: {
            'campaign_name': this.createData.campaign_name,
            'start_budget': this.createData.start_budget,
            'max_budget': this.createData.max_budget
          }
        }
      }).then(resp => {
        if (!resp['data']['status']) {
          alert(resp['data']['message']);
        }
        this.getCampaigns();
        // Закрываем модальное окно
        this.toggleModal('modal-create');
      }).catch(function (resp) {
        console.log(resp);
        alert("Не удалось изменить компанию !");
      });
    },
    getCustomersForSelect() {
      var app = this;
      axios({
        method: 'post',
        url: '/api/v1/customers',
        data: {
          // Data
          'requestType': 'getCustomersForSelect',
          'customersFilter': app.customersFilter
        }
      }).then(function (resp) {
        // account data

        console.log(resp);

        app.customers = resp.data;
      }).catch(function (resp) {
        alert("Could not load customers !");
      });
    },
    deleteItem() {
      let app = this;
      axios({
        method: 'post',
        url: '/api/v1/campaign/delete/' + this.actionItemId,
      }).then(resp => {
        if (!resp['data']['status']) {
          alert(resp['data']['message']);
        } else {
          app.getCampaigns(app.pagination.current_page);
          app.toggleModal('delete');
        }
      }).catch(function (resp) {
        alert("Не удалось удалить запись ! ");
      });
    },

    importCampaigns() {
      var app = this;
      axios({
        method: 'POST',
        url: '/api/v1/campaigns/adw/get',
        data: {
          'customers_ids': app.customers_ids
        }
      }).then(function (resp) {

        if (!resp['data']['status']) {
          alert(resp['data']['message']);
        }

        console.log(resp['data']['status']);
        app.getCampaigns(app.pagination.current_page);
      }).catch(function (resp) {
        console.log(resp);
        alert(resp);
      });
    },
    changeActive(e, elementId) {
      let checked = e.target.checked;
      axios({
        method: 'post',
        url: '/api/v1/campaign/update/' + elementId,
        data: {
          campaign_data: {
            'active': checked ? '1' : '0'
          }
        }
      }).then(resp => {
        if (!resp['data']['status']) {
          alert("Не удалось изменить параметр Active ! " + resp['data']['message']);
        }
      }).catch(function (resp) {
        console.log(resp);
        alert("Не удалось изменить параметр Active !");
      });
    },
    changePage(page = 1) {


      this.pagination.current_page = page;

      this.getCampaigns(page);
    },
    changeSort(field) {
      this.sort.sortField = field;
      this.sort.type = !this.sort.type;
      this.getCampaigns();
    },
    sortIconShow(field) {
      if (this.sort.type === true && this.sort.sortField == field) {
        return '▼';
      } else if (this.sort.type !== true && this.sort.sortField == field) {
        return '▲';
      } else {
        return '';
      }
    },
    toggleModal(type = 'create', colData = false) {

      // todo
      let modal = document.querySelector('.modal-import')
      switch (type) {
        case 'modal-create' :
          this.actionItemId = false;
          this.createData.campaign_name = '';
          this.createData.start_budget = '';
          this.createData.max_budget = '';
          modal = document.querySelector('.modal-create')
          break;
        case 'delete' :
          modal = document.querySelector('.modal-delete')
          break;
        case 'edit':
          if (colData) {
            this.actionItemId = colData.idCampaign;
            this.createData.campaign_name = colData.campaign_name;
            this.createData.start_budget = colData.start_budget;
            this.createData.max_budget = colData.max_budget;
          }
          modal = document.querySelector('.modal-create')
          break;
        case 'edit-campaign-link':
          if (colData && colData.idCampaign) {
            this.coldata = colData;
            // Получаем данные для окна настроек привязки
            this.getsuitablepairs(colData.idCampaign);
          }
          // Отображение необходимого нам окна в зависимости от ситуации.
          modal = document.querySelector('.modal-link-edit')
          break;
        case 'change-status':
          this.campaignSatusModal.adwStatus = 'Не получено';
          modal = document.querySelector('.modal-change-status');
          this.campaignSatusModal.currentStatus = colData.status;
          this.campaignSatusModal.currentCampaignId = colData.idCampaign;
          break;
      }
      // Popup modal window
      var app = this;
      // Work with popup window directly
      const body = document.querySelector('body')

      modal.classList.toggle('opacity-0')
      modal.classList.toggle('pointer-events-none')
      body.classList.toggle('modal-active')

      // Close buttons
      const overlay = modal.querySelector('.modal-overlay')
      overlay.addEventListener('click', () => {
        app.toggleModal(type)
      })
      var closemodal = modal.querySelectorAll('.modal-close')
      closemodal.forEach((el) => {
        el.addEventListener('click', () => {
          app.toggleModal(type)
        })
      });

      // Close window by escape button
      document.onkeydown = function (evt) {
        evt = evt || window.event
        var isEscape = false
        if ("key" in evt) {
          isEscape = (evt.key === "Escape" || evt.key === "Esc")
        } else {
          isEscape = (evt.keyCode === 27)
        }
        if (isEscape && document.body.classList.contains('modal-active')) {
          app.toggleModal(type)
        }
      };
    },
    getsuitablepairs(campaignId) {
      var app = this;
      axios({
        method: 'get',
        url: '/api/v1/campaign/getsuitablepairs/' + campaignId,
      }).then(function (resp) {
        if (resp['data']['status'] === false) {
          console.log(resp['data']['message']);
          alert("Не удалось получить допустимые связанные кампании");
        }
        app.suitablePairs = resp.data.data;
        /*console.log( app.suitablePairs );*/
        // app.getCampaigns();
      });
    },
    setpair(unlink = false) {
      var app = this;


      if (this.new_pair_id.length > 1) {
        alert('Укажите одну кампанию!!!');
        return;
      }
      console.log('set link!');
      // current campaign
      console.log(this.coldata.idCampaign);
      // pair campaign
      console.log(this.new_pair_id[0]);

      axios({
        method: 'post',
        url: '/api/v1/campaign/setpair/' + this.coldata.idCampaign,
        data: {
          'unlink': unlink,
          'pairCampaignId': this.new_pair_id[0]
        }
      }).then(function (resp) {
        if (resp['data']['status'] === false) {
          console.log(resp['data']['message']);
          alert("Не удалось связать");
        }
        app.getCampaigns(app.pagination.current_page);
        app.toggleModal('edit-campaign-link');
      });

    },
    adwGetStatus() {
      let app = this;
      // Update campaign status by adwords
      axios({
        method: 'get',
        url: '/api/v1/campaign/adw/get/status/' + this.campaignSatusModal.currentCampaignId,
      }).then(function (resp) {
        console.log(resp);
       // app.getCampaigns(app.pagination.current_page);
       // app.toggleModal('edit-campaign-link');
            if (resp['data']['status'] === false) {
              console.log(resp['data']['message']);
            }
            else{
              console.log(resp['data']['data']);
              app.campaignSatusModal.adwStatus = resp['data']['data'];
            }
      }).catch(function (resp) {
        console.log(resp);

        alert("Не удалось получить статус кампании с adwords");
      });
    },

    saveStatus(){
      let app = this;
      axios({
        method: 'post',
        url: '/api/v1/campaign/savestatus/' + this.campaignSatusModal.currentCampaignId,
        data: {
          'newStatus': this.campaignSatusModal.currentStatus,
        }
      }).then(function (resp) {
        if (resp['data']['status'] === false) {
          console.log(resp['data']['message']);
          alert("Не удалось изменить статус.");
        }
        app.toggleModal('change-status');
        app.getCampaigns(app.pagination.current_page);
      });
    }

  },
  mounted() {
    this.getCampaigns();
    this.getCustomersForSelect();
  }
}
</script>
