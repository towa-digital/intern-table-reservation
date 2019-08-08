<template>
  <div class="hello">
    <table></table>

    <table>
        <tr>
            <th>Name</th>
            <th>Anzahl Sitze</th>
            <th>Im Außenbereich</th>
        </tr>
        <tr v-for="table in table" v-bind:key="table">
          <td>{{ table.slug }}</td>
          <td>{{ table.acf.seats }}</td>
          <td>{{ table.acf.isOutside }}</td>
        </tr>
    </table>
  </div>
</template>

<script>
export default {
  name: "ApiTest",
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

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
table {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;

  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);

  margin-top: 10%;
}

td, th {
  border: 1px solid #ddd;
  padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2;}

tr:hover {background-color: #ddd;}

th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #da3743;
  color: white;
}
</style>