<template>
  <div>
    <div class="p-3 sm:px-20 bg-white border-b border-gray-200">
      <div class="text-xs mb-4 text-gray-500">
        <div class="float-left">Дата последнего обновления : 23.02.2022 - 21:30</div><div class="float-right">Версия алгоритма : январь 2022</div>
      </div>
    </div>
    <div class="p-6 sm:px-20 bg-white border-b border-gray-200 bg-gray-100">
      <div class="site-wrapper">
        <div class="pt-table desktop-768">
          <div class="pt-tablecell page-home relative" style="background-position: center; background-size: cover;">

            <div class="container">
              <div class="row">
                <div class="col-xs-12 col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8 h-96 pl-14">

                  <div class="hexagon-menu clear">
                    <div class="hexagon-item">
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <a target="_blank" href="/admin/customers_history"  @mouseenter="changeMessage('Результаты еженедельных проверок всех клиентов\n'+
                                                        'за каждый день каждый клиент отображается только один раз, внутри которого находятся все записи этого клиента за определённый день.')"
                         class="hex-content">
                                              <span class="hex-content-inner">
                                                  <span class="icon">
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8"
                                                           fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                           style="margin:0 auto; position:relative; left:0px;">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                  </span>
                                                  <span class="title">Оптимизация клиентов</span>
                                              </span>
                        <svg viewBox="0 0 173.20508075688772 200" height="200" width="174" version="1.1"
                             xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M86.60254037844386 0L173.20508075688772 50L173.20508075688772 150L86.60254037844386 200L0 150L0 50Z"
                              fill="#1e2530"></path>
                        </svg>
                      </a>
                    </div>
                    <div class="hexagon-item">
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <a target="_blank" href="/admin/campaigns_history" @mouseenter="changeMessage('Результаты еженедельных проверок всех кампаний за один день.\n'+
'За каждый день каждая кампания отображается только один раз, внутри которой находятся все записи этой кампании за этот день.')" class="hex-content">
                                              <span class="hex-content-inner">
                                                  <span class="icon">
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8"
                                                           fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                           style="margin:0 auto; position:relative; left:0px;">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                  </span>
                                                  <span class="title">Оптимизация кампаний</span>
                                              </span>
                        <svg viewBox="0 0 173.20508075688772 200" height="200" width="174" version="1.1"
                             xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M86.60254037844386 0L173.20508075688772 50L173.20508075688772 150L86.60254037844386 200L0 150L0 50Z"
                              fill="#1e2530"></path>
                        </svg>
                      </a>
                    </div>
                    <div class="hexagon-item">
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <a target="_blank" href="/admin/campaigns" @mouseenter="changeMessage('Импортирование кампаний в систему через встроенный помощник.')" class="hex-content">
                                              <span class="hex-content-inner">
                                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                                       viewBox="0 0 24 24" stroke="currentColor"
                                                       style="margin:0 auto; position:relative; left:0px;">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"/>
</svg>
                                                  <span class="title">Импорт кампаний</span>
                                              </span>
                        <svg viewBox="0 0 173.20508075688772 200" height="200" width="174" version="1.1"
                             xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M86.60254037844386 0L173.20508075688772 50L173.20508075688772 150L86.60254037844386 200L0 150L0 50Z"
                              fill="#1e2530"></path>
                        </svg>
                      </a>
                    </div>
                    <div class="hexagon-item">
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <a target="_blank" href="/admin/checker-history" @mouseenter="changeMessage('Просмотр логов еженедельных оптимизаций в подробном виде.')" class="hex-content">
                                              <span class="hex-content-inner">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                                      viewBox="0 0 24 24" stroke="currentColor"
                                                      style="margin:0 auto; position:relative; left:0px;">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>
                                                  <span class="title">Логи</span>
                                              </span>
                        <svg viewBox="0 0 173.20508075688772 200" height="200" width="174" version="1.1"
                             xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M86.60254037844386 0L173.20508075688772 50L173.20508075688772 150L86.60254037844386 200L0 150L0 50Z"
                              fill="#1e2530"></path>
                        </svg>
                      </a>
                    </div>
                    <div class="hexagon-item">
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <a target="_blank" href="/admin/customers" @mouseenter="changeMessage('Просмотр, редактирование, добавление клиентов (customers).')" class="hex-content">
                                              <span class="hex-content-inner">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor"
                                                     style="margin:0 auto; position:relative; left:0px;">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
</svg>

                                                  <span class="title">Список клиентов</span>
                                              </span>
                        <svg viewBox="0 0 173.20508075688772 200" height="200" width="174" version="1.1"
                             xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M86.60254037844386 0L173.20508075688772 50L173.20508075688772 150L86.60254037844386 200L0 150L0 50Z"
                              fill="#1e2530"></path>
                        </svg>
                      </a>
                    </div>
                    <div class="hexagon-item">
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <a target="_blank" href="/admin/campaigns" @mouseenter="changeMessage('Просмотр, редактирование, добавление и импорт кампаний.')" class="hex-content">
                                                <span class="hex-content-inner">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10"
                                                      viewBox="0 0 20 20" fill="currentColor"
                                                      style="margin:0 auto; position:relative; left:0px;">
  <path fill-rule="evenodd"
        d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.496-1.868l-7-4zM6 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm3 1a1 1 0 012 0v3a1 1 0 11-2 0v-3zm5-1a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z"
        clip-rule="evenodd"/>
</svg>
                                                  <span class="title">Список кампаний</span>
                                              </span>
                        <svg viewBox="0 0 173.20508075688772 200" height="200" width="174" version="1.1"
                             xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M86.60254037844386 0L173.20508075688772 50L173.20508075688772 150L86.60254037844386 200L0 150L0 50Z"
                              fill="#1e2530"></path>
                        </svg>
                      </a>
                    </div>
                    <div class="hexagon-item">
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <div class="hex-item">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>
                      <a target="_blank" href="/admin/accounts" @mouseenter="changeMessage('Просмотр, редактирование, добавление аккаунтов. OAuth настройки для подключение к adwords.')" class="hex-content">
                                              <span class="hex-content-inner">
                                               <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                    style="margin:0 auto; position:relative; left:0px;">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
</svg>
                                                  <span class="title">Список аккаунтов</span>
                                              </span>
                        <svg viewBox="0 0 173.20508075688772 200" height="200" width="174" version="1.1"
                             xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M86.60254037844386 0L173.20508075688772 50L173.20508075688772 150L86.60254037844386 200L0 150L0 50Z"
                              fill="#1e2530"></path>
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="page-title home text-center h-10">
                  {{message}}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <p class="p-6">Включать и выключать кампании теперь можно из админки</p>

    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
      <div class="p-6">
        <div class="flex items-center">
          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               viewBox="0 0 24 24" class="w-8 h-8 text-gray-400">
            <path
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"><a href="/admin/accounts">Аккаунты</a></div>
        </div>
        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Аккаунты являются частью системы и содержат в себе информацию, необходимую для установки соединения с <b>Adwords</b>
            через <b>Api</b>.
            Также в аккаунтах настраивается почта, на которую будут приходить сообщения в случае ошибок.
          </div>
          <a href="/admin/accounts">
            <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
              <div>Перейти к акаунтам</div>

              <div class="ml-1 text-indigo-500">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                  <path fill-rule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
          </a>
          <a href="/docs/Api token.docx" target="_blank">
            <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
              <div>Инструкция получения api настроек</div>

              <div class="ml-1 text-indigo-500">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                  <path fill-rule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
          </a>
        </div>
      </div>


      <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
        <div class="flex items-center">
          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               viewBox="0 0 24 24" class="w-8 h-8 text-gray-400">
            <path
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"><a href="/admin/customers">Клиенты</a></div>
        </div>
        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Клиенты - это customers из Google Adwords. Это аккаунты по типу 429-XXX-XXXX. Клиентов можно импортировать
            по отдельности.
            <br>Можно импортировать всех сразу, если указать
            id который принадлежит менеджеру, тогда подтянуться все дочерние аккаунты сразу.
            <!--                      <br>Так же можно выбрать для каждого аккаунта активный ли он для оптимизации, является ли он тестовым ( это значит что он будет проверятся и выводится в логи но изменений-->
            <!--                      в адвордсе не будет ), и проверять ли этот аккаунт через каждые 7 дней, или проверяется он всегда.-->
            <!--                      <br>Обратите внимание на то что параметр "активный" глобальный. "7 дней" и "тестовый" будут работать токо если он включен, и наоборот!-->
          </div>
          <a href="/admin/customers">
            <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
              <div>Перейти к Клиентам</div>

              <div class="ml-1 text-indigo-500">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                  <path fill-rule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="p-6 border-t border-gray-200">
        <div class="flex items-center">
          <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
               stroke="currentColor" class="w-8 h-8 text-gray-500">
            <path
                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"><a href="/admin/customers_history">История
            клиентов ( customers )</a></div>
        </div>
        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            На данной странице отображены результаты еженедельных проверок всех клиентов следующим образом : <br>за один
            день сгруппированы все записи каждого клиента.
            За один день каждый клиент отображается только один раз, внутри которого находятся все записи этого клиента
            за этот день.
          </div>
          <a href="/admin/customers_history">
            <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
              <div>Просмотр истории</div>
              <div class="ml-1 text-indigo-500">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                  <path fill-rule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="p-6 border-t border-gray-200 md:border-l">
        <div class="flex items-center">
          <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
               stroke="currentColor" class="w-8 h-8 text-gray-500">
            <path
                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"><a href="/admin/campaigns_history">История
            кампании</a></div>
        </div>
        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            На данной странице отображены результаты еженедельных проверок всех кампаний следующим образом : за один
            день сгруппированы все записи каждой кампании. <br>
            за один день каждая кампания отображается только один раз, внутри которой находятся все записи этой кампании
            за этот день.
          </div>
          <a href="/admin/campaigns_history">
            <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
              <div>Просмотр истории</div>
              <div class="ml-1 text-indigo-500">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                  <path fill-rule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
          </a>
        </div>



      </div>


    </div>
    <p class="p-6">Оптимизация работает. Импорт кампаний работает через страницу "Список кампаний". Страница визарда по импорту кампаний ещё в разработке.ю</p>
  </div>
</template>
<script>
import JetApplicationLogo from '@/Jetstream/ApplicationLogo'

export default {
  data() {
    return {
      // Customers list
      message: '',
      recieveMessage: '',
      timer:null
    }
  },
  components: {
    JetApplicationLogo,
  },
  methods: {
    changeMessage(m) {
      this.recieveMessage = m;
      clearInterval(this.timer);
      this.message = '';
      let i = -1;
      this.timer = setInterval(()=>{
        i++;
        this.message += this.recieveMessage[i] ;
      }, 60-(Math.random()*20))
    }

  },

  watch:{
    message:function(){
      console.log(this.recieveMessage.length + ' - ' + this.recieveMessage.length)
      if (this.message.length >= this.recieveMessage.length){
        clearInterval(this.timer);
      }
    }
  }

}
</script>
