<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Добавление кампаний Шаг1
      </h2>
    </template>
    <!-- [ content ] -->
    <div class="py-6">
      <div class="max-w-9xl mx-auto sm:px-6 lg:px-8 ">
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200 mb-2 rounded">
          <div class="text-gray-500 text-xs">
            Some text here!!
          </div>
        </div>
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
            </div>

            <!-- [ Table ] -->
            <table class="min-w-full">
              <thead>
              <tr>
                <th class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Аккаунт</div>
                    <div class="inline-block min-w-16px"></div>
                  </div>
                </th>

               <th class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider">
                <div class="inline-flex">
                    <div class="inline-block">Клиент</div>
                    <div class="inline-block min-w-16px"></div>
                  </div>
                </th>

                <th class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Id клиента</div>
                    <div class="inline-block min-w-16px"></div>
                  </div>
                </th>

                <th class="cursor-pointer px-3 py-3 border-b-2 border-blue-500 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Менеджер</div>
                    <div class="inline-block min-w-16px"></div>
                  </div>
                </th>

              </tr>
              </thead>
              <tbody name="result-table" is="transition-group">
              <tr class="result-table-item" v-for="col of customers" v-bind:key="col.idCustomer">
                  <td class=" px-3 py-4 border-b border-sky-900 text-blue-900  text-sm leading-5">
                    {{ col.account_name }}
                  </td>
                  <td class=" px-3 py-4 border-b border-sky-900 text-blue-900  text-sm leading-5">
                    {{ col.customer_name }}
                  </td>
                  <td class=" px-3 py-4 border-b border-sky-900 text-blue-900  text-sm leading-5">
                    {{ col.customer_id }}
                  </td>
                  <td class=" px-3 py-4 border-b border-sky-900 text-blue-900  text-sm leading-5">
                    {{ col.can_manage_clients }}
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
      // Customers list
      customers: [],
      // Customer search string
      searchString: '',
      // Sort icon images / asc / des
      sortIcon: '',
      // sort data
      sort: {
        'sortField': 'customers.id',
        'type': false
      },
      vuePagination: {},
      pagination: {
        'total_page': 0,
        'from': 0,
        'to': 0,
        'current_page': 1
      },
      // Selected customers for import
      customers_ids: [],
    }
  },
  components: {
    AppLayout
  },
  methods: {
    changePage(page = 1) {
      this.getCustomers(page);
    },
    getCustomers(page = 1){
      console.log('get customers list');
      var app = this;
      axios({
        method: 'post',
        url: '/api/v1/customers',
        data: {
          'requestType': 'getCustomers',
          'sortField': app.sort.sortField,
          'type': app.sort.type,
          'searchString': app.searchString,
          'page': app.page,
          'itemsPerPage': 50
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

  },
  mounted() {
    this.getCustomers();
  }
}
</script>
