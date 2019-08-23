<template>
  <div>
    <table v-show="step == 2">
      <tr>
        <td colspan="2">
          <h2>
            Verfügbare Tische
            <a>*</a>
          </h2>
        </td>
      </tr>

      <!-- Table 1 -->

      <tr>
        <td colspan="2">
          <InputFormTable ref="tableOne" v-model="tables[0]" />
        </td>
      </tr>

      <!-- Table 2 -->

      <tr>
        <td colspan="2" v-if="inputTwo">
          <InputFormTable ref="tableTwo" v-model="tables[1]" />
        </td>
      </tr>

      <!-- Table 3 -->

      <tr>
        <td colspan="2" v-if="inputThree">
          <InputFormTable ref="tableThree" v-model="tables[2]" />
        </td>
      </tr>

      <!-- Ein Inputfeld -->

      <tr v-if="!inputTwo">
        <td colspan="2">
          <input type="submit" value="Tisch hinzufügen" class="btn" v-on:click="addInputTwo" />
        </td>
      </tr>

      <!-- Zwei Inputfelder -->

      <tr v-if="inputTwo && !inputThree">
        <td class="sized">
          <input type="submit" value="Tisch hinzufügen" class="btn" v-on:click="addInputThree" />
        </td>
        <td class="sized">
          <input type="submit" value="Tisch entfernen" class="btn" v-on:click="removeInput" />
        </td>
      </tr>

      <!-- Drei Inputfelder -->

      <tr v-if="inputThree">
        <td colspan="2">
          <input type="submit" value="Tisch entfernen" class="btn" v-on:click="addInputTwo" />
        </td>
      </tr>

      <!-- Standart Buttons -->

      <tr class="submit">
        <td class="sized">
          <input type="submit" value="Zurück" class="btn" v-on:click="onBack" />
        </td>
        <td class="sized">
          <input type="submit" value="Weiter" class="btn" v-on:click="onGetReservation" />
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <div class="centered" v-if="errormessage != ''">
            <h3 v-html="errormessage"></h3>
          </div>
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import InputFormTable from './../InputFormComponents/InputFormTable';

export default {
  name: 'StepTwo',
  components: {
    InputFormTable,
  },
  data() {
    return {
      tables: [],
      inputTwo: false,
      inputThree: false,
      tablesNumberOfSeats: []
    };
  },
  methods: {
    addInputTwo() {
      this.inputTwo = true;
      this.inputThree = false;
    },
    removeInput() {
      this.inputTwo = false;
      this.InputThree = false;

      this.$refs.tableTwo.freeTable();
      this.$refs.tableThree.freeTable();
    },
    addInputThree() {
      this.inputTwo = true;
      this.inputThree = true;
    },
    onBack() {
      this.$store.commit('setError', '');
      this.$store.commit('decrementStepCounter');
    },
    onGetReservation() {
      let that = this;

      this.getAllTables.forEach(function(data) {
        if (that.tables[0] == data.id) {
          that.tablesNumberOfSeats[0] = data.seats;
        } else if (that.tables[1] == data.id) {
          that.tablesNumberOfSeats[1] = data.seats;
        } else if (that.tables[2] == data.id) {
          that.tablesNumberOfSeats[2] = data.seats;
        }
      });

      var availableSeats = 0;
      var tooMuchTablesForPersons_error = false;
      var tooMuchTablesForPersons_flag = false;

      for (var n of this.tablesNumberOfSeats) {
        if (tooMuchTablesForPersons_flag) tooMuchTablesForPersons_error = true;

        var nosOnTable = n == '' ? 0 : parseInt(n);
        availableSeats += nosOnTable;

        if (availableSeats >= this.getNumberOfSeats) tooMuchTablesForPersons_flag = true;
      }

      if (availableSeats < this.getNumberOfSeats) {
        this.$store.commit('setError', 'Zu wenig Tische für alle Gäste ausgewählt!');
      } else if (tooMuchTablesForPersons_error) {
        this.$store.commit('setError', 'Du hast zu viele Tische ausgewählt!');
      } else {

        this.$store.commit('setStepTwo', this)
        this.$store.commit('incrementStepCounter');
        this.$store.commit('setError', '');
      }
    },
  },
  watch: {
    step(newValue, oldValue) {
      if (newValue === 2 && oldValue === 1) {
        this.$refs.tableOne.onFreeTablesReload();
        if (this.$refs.tableTwo !== undefined) this.$refs.tableTwo.onFreeTablesReload();
        if (this.$refs.tableThree !== undefined) this.$refs.tableThree.onFreeTablesReload();
      }
    },
  },
  computed: {
    ...mapGetters(['errormessage', 'step']),
    getNumberOfSeats() {
      return this.$store.getters.StepOne.numberOfSeats;
    },
    getAllTables() {
      return this.$store.getters.allTables;
    },
  },
};
</script>

<style scoped>
</style>