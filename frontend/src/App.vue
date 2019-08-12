<template>
  <div id="app">
   
    <InputForm v-bind:reservations="reservations" v-on:add-reservation="addReservation"/>
    <!-- <ApiTest /> -->
    
    
  </div>
</template>

<script>
import InputForm from './components/InputForm.vue'
// import ApiTest from './components/ApiTest.vue'


export default {
  name: 'app',
  components: {
   InputForm,
  //  ApiTest
  },
  data() {
    return{
      reservations: [{
        name: "Niklas"
      },
      {
        name: "Elias"
      }]
    }
  },
  methods: {
    addReservation(newReservation) {
      this.reservations = [...this.reservations, newReservation];
    }
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
