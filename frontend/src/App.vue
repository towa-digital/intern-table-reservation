<template>
  <div id="app">
   
    <InputForm />
    <ApiTest />
    
    
  </div>
</template>

<script>
import InputForm from './components/InputForm.vue'
import ApiTest from './components/ApiTest.vue'


export default {
  name: 'app',
  components: {
   InputForm,
   ApiTest
  }
}
</script>

<style>

  body {
    width: 30%;
    margin-left: 35%;
    font-family: sans-serif;

  }

</style>
