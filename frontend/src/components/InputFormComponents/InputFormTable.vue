<template>
  <form>
    <select>
      <option v-for="table in table" v-bind:key="table">{{ table.title.rendered }}</option>
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
  created: function() {
    this.$http.get("wp/v2/tables").then(
      response => {
        for (let table in response.data) {
          this.table.push(response.data[table]);
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


