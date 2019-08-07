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
    width: 20%;
    margin-left: 40%;
    font-family: sans-serif;

  }

</style>
