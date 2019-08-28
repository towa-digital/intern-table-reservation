<template>
  <div id="app">
   
    <InputForm />
    <!-- <ApiTest /> -->
    
    
  </div>
</template>

<script>
import InputForm from './components/InputForm.vue'
// import ApiTest from './components/ApiTest.vue'

import { mapActions } from 'vuex';


export default {
  name: 'app',
  components: {
   InputForm,
  //  ApiTest
  },
  methods: {
    ...mapActions(['loadOptions']),
  },
  created: function() {
    this.loadOptions();
  }
}
</script>

<style>

  body {
    font-family: sans-serif;
  }

</style>
