<template>
  <form>
    <select ref="input" :value="value" @change="updateValue">
      <option disabled selected value>Bitte Tisch auswählen</option>
      <option v-if="selected !== undefined" v-bind:value="selected.id">{{ selected.title }} ({{selected.seats}} Plätze) </option>
      <option v-for="table in freeTables" v-bind:key="table.id" v-bind:value="table.id">{{ table.title }} ({{table.seats}} Plätze) </option>
    </select>
  </form>
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
    freeTable() {
      if(this.selected !== undefined) {
        this.$store.commit("freeTable", this.selected);
      }
    },
    updateValue(event) {
      var selectedId = this.$refs.input.value;
      this.$emit("input", selectedId);

      event.preventDefault();

      if(this.selected !== undefined) {
        this.$store.commit("freeTable", this.selected);
      }
      this.selected = undefined;

      for(var value of this.$store.getters.allTables) {
        if(value["id"] == selectedId) {
          this.selected = value;
        }
      }

      if(this.selected !== undefined) {
        this.$store.commit("claimTable", this.selected);
      }
    },
  }, computed: mapGetters(['freeTables'])
  
};
</script>

<style scoped>
td {
  width: 33%;
}
</style>


