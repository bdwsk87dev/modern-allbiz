<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                История ежедневной проверки по клиентам
            </h2>
        </template>

        <div class="py-12">

            <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
              <div class="p-6 sm:px-20 bg-white border-b border-gray-200 mb-2"><div class="mt-2 text-2xl">
                История еженедельной проверки робота, результаты сгруппированы по клиентам.
              </div> <div class="mt-6 text-gray-500">
                На данной странице отображены результаты проверок всех клиентов следующим образом : за один день сгруппированы все записи каждого клиента. <br>
                За один день каждый клиент отображается только один раз, внутри которого находятся все записи этого клиента за этот день.
              </div></div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-4 pb-4">
                  <div v-if="isExpanded" class="align-middle inline-block min-w-full overflow-hidden bg-white shadow-dashboard px-8 pt-3">
                   <span @click="isExpanded = false" class=" relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer float-right mb-2">
                      <span aria-hidden class="bg-green-200 absolute inset-0 rounded-full"></span>
                      <span class="relative text-xs">Назад</span>
                   </span>

                  <table class="min-w-full mb-80">
                    <thead>
                    <tr>
                      <th class="px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                          <div class="inline-flex">
                          <div class="inline-block">Время</div>
                          <div class="inline-block min-w-16px"></div>
                        </div>
                      </th>
                      <th class="px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider 0">
                        <div class="inline-flex">
                          <div class="inline-block">№ проверки</div>
                          <div class="inline-block min-w-16px"></div>
                        </div>
                      </th>
                      <th class="px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                        <div class="inline-flex">
                          <div class="inline-block">Действие</div>
                          <div class="inline-block min-w-16px"></div>
                        </div>
                      </th>
                      <th class="px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                        <div class="inline-flex">
                          <div class="inline-block">Id кампании</div>
                          <div class="inline-block min-w-16px"></div>
                        </div>
                      </th>
                      <th class="px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                        <div class="inline-flex">
                          <div class="inline-block">Сообщение</div>
                          <div class="inline-block min-w-16px"></div>
                        </div>
                      </th>
                      <th class="px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider text-center">
                        <div class="inline-flex">
                          <div class="inline-block">Категория</div>
                          <div class="inline-block min-w-16px"></div>
                        </div>
                      </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white" is="transition-group" name="result-table" >

                    <tr class="result-table-item"  v-for="customerLog of customerLogs" v-bind:key="customerLog.id">
                      <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5">
                        {{ customerLog.time }}
                      </td>
                      <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5">
                        {{ customerLog.task_id }}
                      </td>
                      <td class="px-3 py-2 border-b text-blue-900 border-gray-500 text-sm leading-5  break-all w-60">
                        {{ customerLog.task }}
                      </td>
                      <td class="px-3 py-2 border-b text-blue-900 border-gray-500 text-sm leading-5  break-all w-60">
                        {{ customerLog.campaign_id}}
                      </td>
                      <td class="px-3 py-2 border-b text-blue-900 border-gray-500 text-sm leading-5  break-all">
                        {{ customerLog.message }}
                      </td>
                      <td class="px-3 py-4 text-center border-b text-blue-900 border-gray-500 text-sm leading-5 w-36">
                                    <span class=" relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                    <span v-bind:class="[customerLog.type == 'processed' ? 'bg-green-200' :  customerLog.type == 'error' ? 'bg-red-600' : 'bg-yellow-200', '']" aria-hidden class="absolute inset-0 rounded-full"></span>
                                    <span v-if="customerLog.type == 'processed'" class="relative text-xs">processed</span>
                                    <span v-else-if="customerLog.type == 'error'" class="relative text-xs text-white">error</span>
                                    <span v-else-if="customerLog.type == 'warning'" class="relative text-xs">warning</span>
                                    <span v-else class="relative text-xs"> </span>
                                    </span>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                  </div>

                  <div v-else class="align-middle inline-block min-w-full overflow-hidden bg-white shadow-dashboard px-8 pt-3">

                        <!-- Pagination -->
                        <div class="sm:flex-1 sm:flex sm:items-center sm:justify-between work-sans">
                            <div>
                                <pagination :limit="2" :data="vuePagination" @pagination-change-page="changePage">
                                    <span slot="prev-nav">&lt; Назад</span>-->
                                    <span slot="next-nav">Вперёд &gt;</span>
                                </pagination>
                            </div>
                              <div class="justify-self-end col-start-2 col-end-5 cursor-pointer select-none self-center">
                                <svg @click="getHistory()" width="18" height="18"
                                     class="absolute ml-48 mt-2.5 pl-2 lg:w-auto"
                                     viewBox="0 0 18 18"
                                     fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path
                                      d="M8.11086 15.2217C12.0381 15.2217 15.2217 12.0381 15.2217 8.11086C15.2217 4.18364 12.0381 1 8.11086 1C4.18364 1 1 4.18364 1 8.11086C1 12.0381 4.18364 15.2217 8.11086 15.2217Z"
                                      stroke="#839fcc" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M16.9993 16.9993L13.1328 13.1328" stroke="#839fcc" stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                                <input @change="getHistory()" type="text" v-model="searchString" placeholder="Поиск"
                                       class="hover:text-blue-500 hidden sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-4 border border-gray-400 rounded ml-2 sm:ml-4 focus:outline-none text-xs sm:text-sm">
                              </div>
                        </div>

                        <table class="min-w-full">
                            <thead>
                            <tr>
                              <th @click="changeSort('Date')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Date</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('date') }}</div>
                                </div>
                              </th>

                              <th @click="changeSort('customer_name')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Название клиента</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('customer_name') }}</div>
                                </div>
                              </th>

                              <th @click="changeSort('customer_id')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Customer id</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('customer_id') }}</div>
                                </div>
                              </th>
                              <th @click="changeSort('task_id')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Task id</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('task_id') }}</div>
                                </div>
                              </th>
                              <th @click="changeSort('total')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Записей</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('total') }}</div>
                                </div>
                              </th>
                              <th @click="changeSort('warnings')"
                                  class="cursor-pointer px-1 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Warnings</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('warnings') }}</div>
                                </div>
                              </th>
                              <th @click="changeSort('errors')"
                                  class="cursor-pointer px-1 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Errors</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('errors') }}</div>
                                </div>
                              </th>
                              <th
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block"></div>
                                  <div class="inline-block min-w-16px"></div>
                                </div>
                              </th>
                            </tr>
                            </thead>



                            <tbody class="bg-white" name="result-table" is="transition-group">
                            <tr class="result-table-item"  v-for="campaign of customerList"  v-bind:key="campaign.id">
                              <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5">
                                {{ campaign.date }}
                              </td>

                              <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5">
                                <p v-if = "campaign.customer_name"> {{campaign.customer_name}}</p>
                                <p class="text-red-400"v-else>Клиента нет в базе</p>
                              </td>

                              <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5">
                                {{ campaign.customer_id }}
                              </td>
                              <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5">
                                {{ campaign.task_id }}
                              </td>
                              <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5">
                                {{ campaign.total }}
                              </td>
                              <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5 text-left">
                                  <span class=" relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                     <span aria-hidden v-bind:class="[campaign.warnings > 0 ? 'bg-yellow-200' : '']"  class="absolute inset-0 rounded-full"></span>
                                    <span class="relative text-xs">{{campaign.warnings}}</span>
                                    </span>
                              </td>
                              <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5 text-left">
                                  <span class=" relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                     <span aria-hidden v-bind:class="[campaign.errors > 0 ? 'bg-red-200' : '']"  class="absolute inset-0 rounded-full"></span>
                                    <span class="relative text-xs">{{campaign.errors}}</span>
                                    </span>
                              </td>
                              <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5 text-right">
                                  <span @click="getCampaign(campaign.customer_id, campaign.date)" class=" relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                  <span aria-hidden class="bg-green-200 absolute inset-0 rounded-full"></span>
                                  <span class="relative text-xs">Посмотреть</span>
                                  </span>
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
                        <p class="text-xs">Отображаются : с {{ pagination.from ? pagination.from : 0 }} по {{
                                pagination.to ? pagination.to : 0
                            }}</p>
                    </div>
                </div>
            </div>
        </div>


    </app-layout>
</template>

<script>
import AppLayout from '../Layouts/AppLayout';
export default {
    data() {
      return{
        // Нажали ли мы на кнопку "посмотреть"
        isExpanded: false,
        // Данные полученные при нажатии на кнопку "Посмотреть"
        customerLogs : [],
        // Campaign list
        customerList: [],
        // user search string
        searchString: '',
        // sort icon images / asc / des
        sortIcon: '',
        // sort data
        sort: {
          'sortField': 'id',
          'type': 'desc'
        },
        vuePagination: {},
        pagination: {
          'total_page': 0,
          'from': 0,
          'to': 0
        },
        filter: {
          'messageType' : 'all'
        },
      }
    },
    methods: {
      changeSort(field) {
        this.sort.sortField = field;
        this.sort.type = !this.sort.type;
        this.getHistory();
      },
      changePage(page = 1) {
        this.getHistory(page);
      },
      getHistory(page = 1){
        var app = this;
        axios({
          method: 'post',
          url: '/api/v1/history/customers/list',
          data: {
            'sortField': this.sort.sortField,
            'type': this.sort.type,
            'searchString': this.searchString,
            'page': page,
            'filterErrorType' : this.filter.messageType
          }
        }).then(function (resp) {
          // Set campaign list
          app.customerList = resp.data.data;

          // data fo laravel-vue-pagination
          app.vuePagination = resp.data;
          // data for simple text pagination
          app.pagination.total_page = resp.data.total;
          app.pagination.from = resp.data.from;
          app.pagination.to = resp.data.to;
        }).catch(function (resp) {
          console.log(resp);
          alert("Could not load logs !");
        }).then(function(){

        });
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

      getCampaign(customer_id, date){
        var app = this;
        app.customerLogs = [];
        this.isExpanded = !this.isExpanded;
        console.log(customer_id, date);
          axios({
            method: 'get',
            url: '/api/v1/history/customer/'+customer_id+'/'+date,
            data: {
            }
          }).then(function (resp) {
            app.customerLogs = resp.data;
          }).catch(function (resp) {
            console.log(resp);
            alert("Could not load logs !");
          }).then(function(){
        });
      },
    },
    components: {
        AppLayout
    },
    mounted() {
      this.getHistory()
    }
}
</script>
