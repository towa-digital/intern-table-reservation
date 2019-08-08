<template>
  <div id="app">
   
    <InputForm />
    
    
  </div>
</template>

<script>
import InputForm from './components/InputForm.vue'


export default {
  name: 'app',
  components: {
   InputForm
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
