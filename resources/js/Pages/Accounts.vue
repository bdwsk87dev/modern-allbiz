<template>

  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Управление аккаунтами
      </h2>
    </template>

    <!-- Create data modal -->
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

            <p v-if="!actionItemId" class="text-2xl font-bold">Создание нового аккаунта</p>
            <p v-else class="text-2xl font-bold">Редактирование аккаунта</p>

            <div class="modal-close cursor-pointer z-50">
              <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                   height="18" viewBox="0 0 18 18">
                <path
                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
              </svg>
            </div>
          </div>

          <!--Body-->

          <p>Название</p>

          <input type="text" v-model="createData.account_name" placeholder="Введите название"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

          <p>Api Client Id</p>

          <input type="text" v-model="createData.client_id" placeholder="Введите clientId"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

          <p>Api Developer Token</p>

          <input type="text" v-model="createData.developer_token" placeholder="Введите developer token"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

          <p>Api Client Secret</p>

          <input type="text" v-model="createData.client_secret" placeholder="Введите client secret"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

          <p>API Refresh Token</p>

          <button @click="generateRefreshToken(actionItemId)"
                  class="p-2 my-2 bg-blue-500 text-white rounded-md focus:outline-none focus:ring-2 ring-blue-300 ring-offset-2">
            Get refresh token
          </button>

          <input type="text" v-model="createData.refresh_token" placeholder="Введите refreshToken"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

          <p>Email для оповещений</p>

          <input type="text" v-model="createData.email" placeholder="Введите refreshToken"
                 class="mt-2 mb-2 hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">

          <p>Описание</p>

          <textarea type="text" v-model="createData.description" placeholder="Введите описание"
                    class="mt-2 mb-2 w-full resize-none hover:text-blue-500 bg-transparent text-gray-600 py-2 hover:text-white px-3 border border-gray-400 rounded focus:outline-none text-xs sm:text-sm">
                    </textarea>

          <!--Footer-->
          <div class="flex justify-end pt-2">
            <button v-if="!actionItemId" @click="create()"
                    class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">
              Создать
            </button>
            <button v-else @click="update()"
                    class="px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">
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

    <!-- Show credentials modal -->
    <div
        class="modal modal-credentials opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
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
            <p class="text-2xl font-bold">Credentials:</p>
            <div class="modal-close cursor-pointer z-50">
              <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                   height="18" viewBox="0 0 18 18">
                <path
                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
              </svg>
            </div>
          </div>
          <!-- Content -->

          <p>Api Client Id :</p>
          <p class="text-xs">{{ itemCredentials.client_id }}</p>
          <p>Api Developer Token :</p>
          <p class="text-xs">{{ itemCredentials.developer_token }}</p>
          <p>Api Client Secret :</p>
          <p class="text-xs">{{ itemCredentials.client_secret }}</p>
          <p>Api Refresh Token : </p>
          <p class="text-xs">{{ itemCredentials.refresh_token }}</p>

          <!--Footer-->
          <div class="flex justify-end pt-2">
            <button class="modal-close px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">
              Закрыть
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
                Создать аккаунт
              </button>
            </li>
            <li class="justify-self-end col-start-2 col-end-5 cursor-pointer select-none self-center pr-8">
              <svg @click="getAccounts()" width="18" height="18"
                   class="absolute ml-48 mt-2.5 pl-2 lg:w-auto"
                   viewBox="0 0 18 18"
                   fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M8.11086 15.2217C12.0381 15.2217 15.2217 12.0381 15.2217 8.11086C15.2217 4.18364 12.0381 1 8.11086 1C4.18364 1 1 4.18364 1 8.11086C1 12.0381 4.18364 15.2217 8.11086 15.2217Z"
                    stroke="#839fcc" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.9993 16.9993L13.1328 13.1328" stroke="#839fcc" stroke-linecap="round"
                      stroke-linejoin="round"/>
              </svg>
              <input @change="getAccounts()" type="text" v-model="searchString" placeholder="Поиск"
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
                <th @click="changeSort('id')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">ID</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('id') }}</div>
                  </div>
                </th>
                <th @click="changeSort('account_name')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Название</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('account_name') }}</div>
                  </div>
                </th>

                <th @click="changeSort('description')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Описание</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('description') }}</div>
                  </div>
                </th>

                <th @click="changeSort('email')"
                    class="cursor-pointer px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  <div class="inline-flex">
                    <div class="inline-block">Email</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('email') }}</div>
                  </div>
                </th>

                <th class="px-3 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  &nbsp;Credentials
                </th>

                <th @click="changeSort('testing')"
                    class="px-0 cursor-pointer py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider w-12">
                  <div class="inline-flex">
                    <div class="inline-block">Тест режим</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('testing') }}</div>
                  </div>
                </th>



                <th @click="changeSort('active')"
                    class="px-0 cursor-pointer py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider w-12">
                  <div class="inline-flex">
                    <div class="inline-block">Aкт.</div>
                    <div class="inline-block min-w-16px">{{ sortIconShow('active') }}</div>
                  </div>
                </th>




                <th class="px-0 py-3 border-b-2 border-gray-300 text-right text-sm leading-4 text-blue-500 tracking-wider w-10">
                  Настройки
                </th>
              </tr>
              </thead>
              <tbody name="result-table" is="transition-group">
              <tr class="result-table-item" v-for="col in accounts" v-bind:key="col.id">
                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 w-10">
                  {{ col.id }}
                </td>
                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all w-40">
                  {{ col.account_name }}
                </td>

                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all w-96">
                  {{ col.description }}
                </td>

                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 break-all w-40">
                  {{ col.email }}
                </td>


                <td class="px-3 py-4  border-b text-blue-900 border-gray-500 text-sm leading-5 w-36">
                                    <span @click="toggleModal('credentials'); itemCredentials = col;"
                                          class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight cursor-pointer">
                                    <span aria-hidden
                                          class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                    <span class="relative text-xs">Посмотреть</span>
                                    </span>
                </td>

                <td class="px-2.5 text-center whitespace-no-wrap border-b border-gray-500 text-blue-900 text-sm leading-5">
                  <input
                      :checked="col.testing"
                      @click="changeTesting($event, col.id)"
                      type="checkbox"
                      class="form-checkbox cursor-pointer">
                </td>


                <td class="px-2.5 whitespace-no-wrap border-b border-gray-500 text-blue-900 text-sm leading-5">
                  <input
                      :checked="col.active"
                      @click="changeActive($event, col.id)"
                      type="checkbox"
                      class="form-checkbox cursor-pointer">
                </td>



<!--                <td class="px-2.5 whitespace-no-wrap border-b border-gray-500 text-blue-900 text-sm leading-5">-->
<!--                  <input-->
<!--                      :checked="col.seven_days"-->
<!--                      @click="changeSevenDays($event, col.id)"-->
<!--                      type="checkbox"-->
<!--                      class="form-checkbox cursor-pointer">-->
<!--                </td>-->

                <td class="px-0 py-4 whitespace-no-wrap text-right border-b border-gray-500 text-sm leading-5">
                  <button @click="toggleModal('edit', col)"
                          class="px-3 py-2  text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none">
                    <font-awesome-icon icon="cogs" class="icon alt"/>
                  </button>
                  <button @click="toggleModal('delete', col);"
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
            <p class="mt-5 text-xs">Всего аккаунтов : {{ pagination.total_page }}</p>
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
      // account list
      accounts: [],
      // user search string
      searchString: '',
      // sort icon images / asc / des
      sortIcon: '',
      // sort data
      sort: {
        'sortField': 'id',
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
      // user data from create form
      createData: {
        // Data
        'account_name': '',
        'description': '',
        // Api data
        'client_id': '',
        'developer_token': '',
        'client_secret': '',
        'refresh_token': '',
        'email': ''
      },
      actionItemId: false,
      itemCredentials: ''
    }
  },
  components: {
    AppLayout
  },
  methods: {
    generateRefreshToken(actionItemId) {
      var app = this;
      console.log('generateRefreshToken');
      axios({
        method: 'post',
        url: '/api/v1/accounts/generate-refresh-token',
        data: {
          'data': this.createData,
        }
      }).then(function (resp) {
        window.open(resp.data, '_blank');
        console.log(resp.data);
      }).catch(function (resp) {
        console.log(resp);
        alert("Error !");
      });
    },
    getAccounts(page = 1) {
      var app = this;
      axios({
        method: 'post',
        url: '/api/v1/accounts/list',
        data: {
          'sortField': this.sort.sortField,
          'type': this.sort.type,
          'searchString': this.searchString,
          'page': page
        }
      }).then(function (resp) {
        // account data
        app.accounts = resp.data.data;
        // data fo laravel-vue-pagination
        app.vuePagination = resp.data;
        // data for simple text pagination
        app.pagination.total_page = resp.data.total;
        app.pagination.from = resp.data.from;
        app.pagination.to = resp.data.to;
        console.log(resp.data);
      }).catch(function (resp) {
        console.log(resp);
        alert("Could not load accounts !");
      });
    },
    update() {
      axios({
        method: 'POST',
        url: '/api/v1/accounts/update/' + this.actionItemId,
        data: {
          account_data: {
            'account_name': this.createData.account_name,
            'description': this.createData.description,
            'client_id': this.createData.client_id,
            'developer_token': this.createData.developer_token,
            'client_secret': this.createData.client_secret,
            'refresh_token': this.createData.refresh_token,
            'email': this.createData.email
          }
        }
      }).then(resp => {
        if (!resp['data']['status']) {
          alert(resp['data']['message']);
        }
        // Получаем по новой список аккаунтов
        this.getAccounts();
        // Закрываем модальное окно
        this.toggleModal();
      }).catch(function (resp) {
        console.log(resp);
        alert("Не удалось изменить аккаунт !");
      });
    },
    deleteItem() {
      axios({
        method: 'POST',
        url: '/api/v1/accounts/delete/' + this.actionItemId
      }).then(resp => {
        if (!resp['data']['status']) {
          alert(resp['data']['message']);
        } else {
          // Получаем по новой список аккаунтов
          this.getAccounts();
          // Закрываем модальное окно
          this.toggleModal('delete');
        }
      }).catch(function (resp) {
        alert("Не удалось удалить запись ! ");
      });
    },
    create() {
      // Отправляем запрос на создание аккаунта
      axios({
        method: 'post',
        url: '/api/v1/accounts/create',
        data: {
          'data': this.createData
        }
      }).then(response => {
        // Получаем по новой список аккаунтов
        this.getAccounts();
        // Закрываем модальное окно
        this.toggleModal();
      }).catch(function (resp) {
        console.log(resp);
        alert("Не удалось создать аккаунт!");
      });
    },
    changeActive(e, elementId) {
      let checked = e.target.checked;
      axios({
        method: 'post',
        url: '/api/v1/accounts/update/' + elementId,
        data: {
          account_data: {
            'active': checked ? '1' : '0'
          }
        }
      }).then(resp => {
        if (!resp['data']['status']) {
          alert(resp['data']['message']);
        }
      }).catch(function (resp) {
        console.log(resp);
        alert("Не удалось изменить параметр Active!");
      });
    },
    changeTesting(e, elementId) {
      let checked = e.target.checked;
      axios({
        method: 'post',
        url: '/api/v1/accounts/update/' + elementId,
        data: {
          account_data: {
            'testing': checked ? '1' : '0'
          }
        }
      }).then(resp => {
        if (!resp['data']['status']) {
          alert(resp['data']['message']);
        }
      }).catch(function (resp) {
        console.log(resp);
        alert("Не удалось изменить параметр Active!");
      });
    },
    changeSevenDays(e, elementId) {
      let checked = e.target.checked;
      axios({
        method: 'post',
        url: '/api/v1/accounts/update/' + elementId,
        data: {
          account_data: {
            'seven_days': checked ? '1' : '0'
          }
        }
      }).then(resp => {
        if (!resp['data']['status']) {
          alert(resp['data']['message']);
        }
      }).catch(function (resp) {
        console.log(resp);
        alert("Не удалось изменить параметр Active!");
      });
    },
    changePage(page = 1) {
      this.getAccounts(page);
    },
    changeSort(sortField) {
      this.sort.sortField = sortField;
      this.sort.type = !this.sort.type;
      this.getAccounts();
    },
    sortIconShow(sortField) {
      if (this.sort.type === true && this.sort.sortField == sortField) {
        return '▼';
      } else if (this.sort.type !== true && this.sort.sortField == sortField) {
        return '▲';
      } else {
        return '';
      }
    },
    toggleModal(type = 'create', colData = false) {
      let modal = document.querySelector('.modal-create')
      switch (type) {
        case 'create':
          this.actionItemId = false;
          this.createData.account_name = '';
          this.createData.description = '';
          this.createData.client_id = '';
          this.createData.developer_token = '';
          this.createData.client_secret = '';
          this.createData.refresh_token = '';
          this.createData.email = '';
          break;
        case 'delete' :
          if (colData) this.actionItemId = colData.id;
          modal = document.querySelector('.modal-delete')
          break;
        case 'credentials':
          modal = document.querySelector('.modal-credentials')
          break;
        case 'edit':
          if (colData) {
            this.actionItemId = colData.id;
            this.createData.account_name = colData.account_name;
            this.createData.description = colData.description;
            this.createData.client_id = colData.client_id;
            this.createData.developer_token = colData.developer_token;
            this.createData.client_secret = colData.client_secret;
            this.createData.refresh_token = colData.refresh_token;
            this.createData.email = colData.email;
          }
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
    }
  },
  mounted() {
    this.getAccounts();
  },
  computed: {}
}
</script>
