<template>
  <div>
    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
      <div class="mt-2 text-2xl">
        Система для автоматической оптимизации adwords кампаний.
      </div>
      <div class="mt-6 text-gray-500">
        В данном приложении можно настроить автоматическую проверку рекламных компаний <b>adwords</b> согласно <b>алгоритму
        проверки 2.0</b>
        <br>
        Версия системы : 13012022_1137<br>
        Версия алгоритма : январь 2022
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
  </div>
</template>
<script>
import JetApplicationLogo from '@/Jetstream/ApplicationLogo'

export default {
  components: {
    JetApplicationLogo,
  },
}
</script>
