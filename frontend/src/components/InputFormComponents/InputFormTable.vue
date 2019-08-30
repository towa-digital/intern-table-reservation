<template>
  <div>
    <select ref="input" :value="value" @change="updateValue">
      <option disabled selected value>Bitte Tisch auswählen</option>
      <option v-if="selected !== undefined" v-bind:value="selected.id">{{ selected.title }} ({{selected.seats}} Plätze) </option>
      <option v-for="table in freeTables" v-bind:key="table.id" v-bind:value="table.id">{{ table.title }} ({{table.seats}} Plätze) </option>
    </select>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: "InputFormTable",
  data() {
    return {
      table: [],
      selected: undefined
    };
  },
  props: {
    value: {
      type: String
    }
  },
  methods: {
    /*
     * When a InputFormTable is removed from the DOM, it have to return it's selected
     * value to freeTables.
     */ 
    freeTable() {
      if(this.selected !== undefined) {
        this.$store.commit("freeTable", this.selected);
      }
    },
    onFreeTablesReload() {
      if(this.selected === undefined) return;

      var indexOf = -1;
      
      for(var i in this.$store.getters.freeTables) {
        if(this.$store.getters.freeTables[i]["id"] == this.selected["id"]) {
          indexOf = i;
        }
      }

      if(indexOf != -1) {
        this.$store.commit("claimTable", this.selected);
      } else {
        this.selected = undefined;
      }
    },
    updateValue(event) {
      var selectedId = this.$refs.input.value;

      var selectedObj = undefined;
      // Table-Objekt zu ausgewählter ID abfragen
      for(var table of allTables) {
        if(table.id == selectedId) selectedObj = table;
      }
      this.$emit("input", selectedObj);

      event.preventDefault();

      if(this.selected !== undefined) {
        this.$store.commit("freeTable", this.selected);
      }
      this.selected = selectedObj;

      if(this.selected !== undefined) {
        this.$store.commit("claimTable", this.selected);
      }
    },
  }, computed: mapGetters(['freeTables', "allTables"])
  
};
</script>

<style scoped>
td {
  width: 33%;
}
</style>


