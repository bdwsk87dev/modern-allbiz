<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                История ежедневной проверки
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-4 pb-4">
                    <div class="align-middle inline-block min-w-full overflow-hidden bg-white shadow-dashboard px-8 pt-3">
                        <!-- Pagination -->

                        <div class="sm:flex-1 sm:flex sm:items-center sm:justify-between work-sans">
                            <div>
                                <pagination :limit="2" :data="vuePagination" @pagination-change-page="changePage">
                                    <span slot="prev-nav">&lt; Назад</span>-->
                                    <span slot="next-nav">Вперёд &gt;</span>
                                </pagination>
                            </div>



                              <div class="justify-self-end col-start-2 col-end-5 cursor-pointer select-none self-center">
                                <svg @click="getLogs(1)" width="18" height="18"
                                     class="absolute ml-48 mt-2.5 pl-2 lg:w-auto"
                                     viewBox="0 0 18 18"
                                     fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path
                                      d="M8.11086 15.2217C12.0381 15.2217 15.2217 12.0381 15.2217 8.11086C15.2217 4.18364 12.0381 1 8.11086 1C4.18364 1 1 4.18364 1 8.11086C1 12.0381 4.18364 15.2217 8.11086 15.2217Z"
                                      stroke="#839fcc" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M16.9993 16.9993L13.1328 13.1328" stroke="#839fcc" stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                                <input @change="getLogs(1)" type="text" v-model="searchString" placeholder="Поиск"
                                       class="hover:text-blue-500 hidden sm:block bg-transparent text-gray-600 py-2 hover:text-white sm:py-2 px-4 border border-gray-400 rounded ml-2 sm:ml-4 focus:outline-none text-xs sm:text-sm">
                              </div>



                        </div>

                        <table class="min-w-full">
                            <thead>
                            <tr>

                                <th @click="changeSort('task_id')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Task id</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('task_id') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('created_at')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Date</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('created_at') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('customer_id')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Customer</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('customer_id') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('campaign_id')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                                    <div class="inline-flex">
                                        <div class="inline-block">Campaign</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('campaign_id') }}</div>
                                    </div>
                                </th>

                                <th @click="changeSort('message')"
                                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider ">
                                    <div class="inline-flex">
                                        <div class="inline-block">Message</div>
                                        <div class="inline-block min-w-16px">{{ sortIconShow('message') }}</div>
                                    </div>
                                </th>

                              <th @click="changeSort('type')"
                                  class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-center text-sm leading-4 text-blue-500 tracking-wider">
                                <div class="inline-flex">
                                  <div class="inline-block">Type</div>
                                  <div class="inline-block min-w-16px">{{ sortIconShow('type') }}</div>
                                </div>
                              </th>

                            </tr>
                            </thead>

                            <tbody class="bg-white">

                                <tr v-for="log of logs">
                                    <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5 w-12">
                                        {{ log.task_id }}
                                    </td>
                                    <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5 w-16">
                                        {{ log.created_at }}
                                    </td>

                                    <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5 w-16">
                                        {{ log.customer_id }}
                                    </td>
                                    <td class="px-3 py-2  border-b text-blue-900 border-gray-500 text-sm leading-5 w-16">
                                        {{ log.campaign_id }}
                                    </td>

                                    <td class="px-3 py-2 border-b text-blue-900 border-gray-500 text-sm leading-5 w-80 break-all">
                                        {{ log.message }}
                                    </td>

                                  <td class="px-3 py-4 text-center border-b text-blue-900 border-gray-500 text-sm leading-5 w-36">
                                    <span class=" relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                    <span v-bind:class="[log.type == 'processed' ? 'bg-green-200' :  log.type == 'error' ? 'bg-red-600' : 'bg-yellow-200', '']" aria-hidden class="absolute inset-0 rounded-full"></span>
                                    <span v-if="log.type == 'processed'" class="relative text-xs">processed</span>
                                    <span v-else-if="log.type == 'error'" class="relative text-xs text-white">error</span>
                                    <span v-else-if="log.type == 'warning'" class="relative text-xs">warning</span>
                                      <span v-else-if="log.type == 'success'" class="relative text-xs">success</span>
                                    <span v-else class="relative text-xs"> </span>
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
        return {
            // Logs list
            logs: [],
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
        }
    },

    methods: {
        changeSort(field) {
            this.sort.sortField = field;
            this.sort.type = !this.sort.type;
            this.getLogs();
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
        getLogs(page = 1) {
            var app = this;
            axios({
                method: 'post',
                url: '/api/v1/daily-task-log/list',
                data: {
                    'sortField': this.sort.sortField,
                    'type': this.sort.type,
                    'searchString': this.searchString,
                    'page': page
                }
            }).then(function (resp) {
                // account data
                app.logs = resp.data.data;
                // data fo laravel-vue-pagination
                app.vuePagination = resp.data;
                // data for simple text pagination
                app.pagination.total_page = resp.data.total;
                app.pagination.from = resp.data.from;
                app.pagination.to = resp.data.to;
            }).catch(function (resp) {
                console.log(resp);
                alert("Could not load logs !");
            });
        },
        changePage(page = 1) {
            this.getLogs(page);
        },
    },
    components: {
        AppLayout
    },
    mounted() {
        this.getLogs();
    }
}
</script>
