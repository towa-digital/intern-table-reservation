<template>
  <form>
    <select ref="input" :value="value" @change="updateValue">
      <option disable selected value>Bitte Tisch auswählen</option>
      <option v-for="table in allTables" v-bind:key="table.id" >{{ table.id }}</option>
    </select>
  </form>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: "InputFormTable",
  data() {
    return {
      table: []
    };
  },
  props: {
    value: {
      type: String
    }
  },
  methods: {
    updateValue(event) {
      this.$emit("input", this.$refs.input.value);
      event.preventDefault();
    },
  }, computed: mapGetters(['allTables'])
  
};
</script>

<style>
</style>


