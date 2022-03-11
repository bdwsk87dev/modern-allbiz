<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Кампании
      </h2>
    </template>
    Content here!
  </app-layout>
</template>
<script>
import AppLayout from '../Layouts/AppLayout';
export default {
 name: "AdwPMList",
  data() {
    return {
      // Current products page
      page: 1,
      // Products array
      products:[],
    }
  },
  methods: {
    getPoorProducts(page) {
      let app = this;
      axios({
        method: 'post',
        url: '/api/v1/products/poor/list',
        data: {
          'page': page
        }
      }).then(function (resp) {
        console.log('done!');
      });
    }
  },
  components: {
    AppLayout
  },
  mounted() {
   // Get poor products page one
    this.getPoorProducts(1);
  }
}
</script>
<style scoped>
</style>