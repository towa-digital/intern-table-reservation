<template>
  <form>
    <select ref="input" :value="value" @change="updateValue">
      <option disable selected value>Bitte Tisch auswählen</option>
      <option v-for="tables in table" v-bind:key="tables" >{{ tables.id }}</option>
    </select>
  </form>
</template>

<script>
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
    }
  },
  created: function() {
    this.$http.get("wp/v2/tables").then(
      response => {
        for (let tables in response.data) {
          this.table.push(response.data[tables]);
        }
      },
      error => {
        alert(error);
      }
    );
  }
};
</script>

<style>
</style>


