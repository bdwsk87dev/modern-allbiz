<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Управление клиентами
            </h2>
        </template>

        <!-- Edit customer modal -->
        <div
            class="modal modal-edit opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
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

                        <p class="text-2xl font-bold">Редактирование клиента</p>

                        <div class="modal-close cursor-pointer z-50">
                            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                                 height="18" viewBox="0 0 18 18">
                                <path
                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                            </svg>
                        </div>
                    </div>

                    <p>Начальный бюжет</p>
                    <input v-model="createData.start_budget"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           type="text" placeholder="Начальный бюджет"
                           class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

                    <p>Максимальный бюжет</p>
                    <input v-model="createData.max_budget"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           type="text" placeholder="Максимальный бюджет"
                           class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

                    <!--Footer-->
                    <div class="flex justify-end pt-2">
                        <button  @click="update()" class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">
                            Сохранить
                        </button>
                        <button class="modal-close px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
                            Отмена
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete confirm modal -->
        <div class="modal modal-delete opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
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
                        <p class="text-2xl font-bold">Удаление аккаунта</p>
                        <div class="modal-close cursor-pointer z-50">
                            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                                 height="18" viewBox="0 0 18 18">
                                <path
                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- Content -->
                    <p>Вы действительно хотите удадалить этот аккаунт ?</p>

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

        <!-- Import customers result modal -->
        <div
            class="modal modal-import-status opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
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
                        <p class="text-2xl font-bold">Импортирование аккаунтов</p>
                        <div class="modal-close cursor-pointer z-50">
                            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                                 height="18" viewBox="0 0 18 18">
                                <path
                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- Content -->
                    <p>Импортировано новых клиентов :</p>
                    <p>{{importStatus.newCustomers}}</p>
                    <p>Уже ранее импортированные клиенты :</p>
                    <p>{{importStatus.issetCustomers}}</p>

                    <!--Footer-->
                    <div class="flex justify-end pt-2">
                        <button class="modal-close px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
                            Понятно
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Import customers -->
        <div
            class="modal modal-import opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
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
                        <p class="text-2xl font-bold">Импортирование клиентов</p>
                        <div class="modal-close cursor-pointer z-50">
                            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                                 height="18" viewBox="0 0 18 18">
                                <path
                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- Content -->
                    <label>Выбор настроек:</label>
                    <select v-model='account_id' class="w-full hover:text-blue-500 sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-2 border border-gray-400 rounded  focus:outline-none text-xs sm:text-sm">
                        <option class="text-black" v-for="account in this.accounts" v-bind:value="account.id">{{account.id}} _ {{account.account_name}}</option>
                    </select>
                    <br>
                    <label>ID клиента:</label>
                    <input v-model='customer_id' type="text" placeholder="xxx-xxx-xxxx"  class="w-full hover:text-blue-500 sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-2 border border-gray-400 rounded  focus:outline-none text-xs sm:text-sm">
                    <br>

                    <p>Введите Id клиента и в систему будут импортированы все связанные клиенты ( если это менеджер )</p>

                    <!--Footer-->
                    <div class="flex justify-end pt-2">
                        <button @click="importCustomers()"
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

        <div class="py-12">
            <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-4 pb-4">
                    <ul class="grid grid-cols-4">
                        <li class="col-start-1 col-end-1 cursor-pointer select-none self-center hover:black">
                            <button
                                @click="toggleModal('create')"
                                class="ml-8 hover:text-blue-500 bg-transparent hover:bg-secondary text-gray-600 py-2 hove:bg-gray-800 hover:text-white px-4 border border-gray-400 rounded ml-2 focus:outline-none text-xs">
                                Импортировать клиентов
                            </button>
                        </li>
                      <li class="justify-self-end col-start-2 col-end-5 cursor-pointer select-none self-center pr-8">
                        <svg @click="getCustomers()" width="18" height="18" class="absolute ml-48 mt-2.5 pl-2 lg:w-auto"
                             viewBox="0 0 18 18"
                             fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M8.11086 15.2217C12.0381 15.2217 15.2217 12.0381 15.2217 8.11086C15.2217 4.18364 12.0381 1 8.11086 1C4.18364 1 1 4.18364 1 8.11086C1 12.0381 4.18364 15.2217 8.11086 15.2217Z"
                              stroke="#839fcc" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M16.9993 16.9993L13.1328 13.1328" stroke="#839fcc" stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>
                        <input @change="getCustomers()" type="text" v-model="searchString" placeholder="Поиск"
                               class="hover:text-blue-500 hidden sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-4 border border-gray-400 rounded ml-2 sm:ml-4 focus:outline-none text-xs sm:text-sm">
                      </li>
                    </ul>

                    <div
                        class="align-middle inline-block min-w-full overflow-hidden bg-white shadow-dashboard px-8 pt-3">

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

                              <th @click="changeSort('customers.id')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">ID</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('customers.id') }}</div>
                                </div>
                              </th>

                                <th @click="changeSort('accounts.account_name')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Аккаунт</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('accounts.account_name') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('customers.customer_name')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                  <div class="inline-flex">
                                    <div class="inline-block">Название</div>
                                    <div class="inline-block min-w-16px">{{ sortIconShow('customers.customer_name') }}</div>
                                </div>
                                </th>


                              <th @click="changeSort('customers.customer_id')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Id клиента</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('customers.customer_id') }}</div>
                                </div>
                              </th>

                                <th @click="changeSort('customers.start_budget')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Ст. бюджет</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('customers.start_budget') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('customers.max_budget')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Макс бюджет</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('customers.max_budget') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('customers.last_check_date')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Посл. проверка</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('customers.last_check_date') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('campaigns.can_manage_clients')"
                                    class="cursor-pointer px-8 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Менеджер</div>
                                        <div v-if="customers.can_manage_clients" class="inline-block min-w-16px">{{ sortIconShow('') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('customers.active')"
                                  class="px-0 cursor-pointer py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider w-12">
                                <div class="inline-flex">
                                  <div class="inline-block">Aкт.</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('customers.active') }}</div>
                                </div>
                              </th>
                              <th class="px-0 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider w-10">
                                Настройки
                              </th>

                            </tr>
                            </thead>
                            <tbody name="result-table" is="transition-group">
                            <tr class="result-table-item" v-for="col of customers" v-bind:key="col.idCustomer">
                                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5">
                                    {{ col.idCustomer }}
                                </td>

                                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all">
                                    {{ col.account_name }}
                                </td>

                                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all">
                                    {{ col.customer_name }}
                                </td>

                                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all">
                                    {{ col.customer_id }}
                                </td>

                                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all">
                                    {{ col.start_budget }}
                                </td>

                                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all">
                                    {{ col.max_budget }}
                                </td>

                                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all">
                                    {{ col.last_check_date }}
                                </td>

                                <td class="px-3 py-4 text-center border-b text-blue-900 border-gray-500 text-sm leading-5 w-36">
                                    <span
                                        class=" relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">

                                    <span v-bind:class="[col.can_manage_clients ? 'bg-yellow-200' : 'bg-green-200', '']" aria-hidden class="absolute inset-0  opacity-50 rounded-full"></span>
                                    <span v-if="col.can_manage_clients" class="relative text-xs">Менеджер</span>
                                    <span v-else class="relative text-xs">Нет</span>
                                    </span>
                                </td>

                              <td class="px-2.5 whitespace-no-wrap border-b border-gray-500 text-blue-900 text-sm leading-5">
                                <input
                                    :checked="col.active"
                                    @click="changeActive($event, col.idCustomer)"
                                    type="checkbox"
                                    class="form-checkbox cursor-pointer">
                              </td>
                              <td class="px-0 py-4 whitespace-no-wrap text-right border-b border-gray-500 text-sm leading-5">
                                <button @click="toggleModal('edit', col)"
                                    class="px-3 py-2  text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none">
                                  <font-awesome-icon icon="cogs" class="icon alt"/>
                                </button>
                                <button @click="toggleModal('delete'); actionItemId = col.idCustomer;"
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
                        <p class="text-xs">Отображаются : с {{ pagination.from? pagination.from : 0 }} по {{ pagination.to ? pagination.to:0  }}</p>

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
            // Customers list
            customers: [],
            // All accounts for import
            accounts: [],
            // user search string
            searchString: '',
            // sort icon images / asc / des
            sortIcon: '',
            // sort data
            sort: {
                'sortField': 'customers.id',
                'type': false
            },
            // pagination data for laravel-vue-pagination
            vuePagination: {},
            // pagination data for simple text
            pagination: {
                'total_page': 0,
                'from': 0,
                'to': 0
            },
            account_id:'',
            customer_id:'',
            actionItemId:'',
            importStatus: {
                newCustomers: 0,
                issetCustomers: 0
            },
            createData: {
                'start_budget': '',
                'max_budget': ''
            },
        }
    },
    components: {
        AppLayout
    },
    methods: {
        getCustomers(page = 1) {
            var app = this;
            axios({
                method: 'post',
                url: '/api/v1/customers',
                data: {
                    // Data
                    'requestType': 'getCustomers',
                    'sortField': this.sort.sortField,
                    'type': this.sort.type,
                    'searchString': this.searchString,
                    // Pagination
                    'page': page
                }
            }).then(function (resp) {
                // account data
                app.customers = resp.data.data;
                // data fo laravel-vue-pagination
                app.vuePagination = resp.data;
                // data for simple text pagination
                app.pagination.total_page = resp.data.total;
                app.pagination.from = resp.data.from;
                app.pagination.to = resp.data.to;
            }).catch(function (resp) {
                console.log(resp);
                alert("Could not load customers !");
            });
        },
        getAccountForSelect(){
            var app = this;
            axios({
                method: 'post',
                url: '/api/v1/accounts/simple_list',
            }).then(function (resp) {
                // account data
                app.account_id = resp.data[0]['id'];
                app.accounts = resp.data;
            }).catch(function (resp) {
                alert("Could not load accounts !");
            });
        },
        importCustomers(){
            var app = this;
            axios({
                method: 'post',
                url: '/api/v1/customers',
                data: {
                    // Data
                    'requestType': 'import-customers-from-api',
                    'account_id': app.account_id,
                    'customer_id': app.customer_id
                }
            }).then(function (resp) {
                console.log(resp['data']['status']);
                if (!resp['data']['status']) {
                    alert("Не удалось импортировать customers ! " + resp['data']['message']);
                }
                else{
                    app.importStatus.newCustomers = resp['data']['newCustomers'];
                    app.importStatus.issetCustomers = resp['data']['issetCustomers'];
                    app.toggleModal('modal-import-status');
                    app.getCustomers();
                }
            }).catch(function (resp) {
                console.log(resp);
                alert("Не удалось импортировать customers");
            });
        },
        deleteItem() {
            var app = this;
            axios({
                method: 'post',
                url: '/api/v1/customers',
                data: {
                    // Data
                    'id': this.actionItemId,
                    'requestType': 'delete'
                }
            }).then(resp => {
                if (!resp['data']['status']) {
                    alert("Не удалось удалить запись ! " + resp['data']['message']);
                } else {
                    this.getCustomers();
                    this.toggleModal('delete');
                }
            }).catch(function (resp) {
                alert(resp);
                alert("Не удалось удалить запись ! ");
            });
        },
        changeActive(e, elementId) {
            let checked = e.target.checked;
            var app = this;
            axios({
                method: 'post',
                url: '/api/v1/customers',
                data: {
                    // Data
                    'id': elementId,
                    'requestType': 'changeActive',
                    'checked': checked
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
            this.getCustomers(page);
        },
        changeSort(field) {
            this.sort.sortField = field;
            this.sort.type = !this.sort.type;
            this.getCustomers();
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
                case 'edit':
                    this.actionItemId = colData.idCustomer;
                    this.createData.start_budget = colData.start_budget;
                    this.createData.max_budget = colData.max_budget;
                    modal = document.querySelector('.modal-edit')
                    break;
                case 'delete' :
                    modal = document.querySelector('.modal-delete')
                    break;
                case 'modal-import-status':
                    modal = document.querySelector('.modal-import-status');
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
        update(){
            axios({
                method: 'post',
                url: '/api/v1/customers',
                data: {
                    // Data
                    'accountId' : this.actionItemId,
                    'requestType': 'update',
                    'start_budget': this.createData.start_budget,
                    'max_budget': this.createData.max_budget
                }
            }).then(resp => {
                if (!resp['data']['status']) {
                    alert(resp['data']['message']);
                }
                this.getCustomers();
                // Закрываем модальное окно
                this.toggleModal('edit');
            }).catch(function (resp) {
                console.log(resp);
                alert("Не удалось сохранить клиента !");
            });
        },
    },
    mounted() {
        this.getCustomers();
        this.getAccountForSelect();
    }
}
</script>
