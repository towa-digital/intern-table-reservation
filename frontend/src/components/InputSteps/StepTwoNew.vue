<template>
<div>
    <TableCanvas v-model="returnedFromCanvas" v-if="showCanvas"></TableCanvas>
    <table v-show="step == 2">
      <tr>
        <td colspan="2">
          <h2>
            Verfügbare Tische
            <a>*</a>
          </h2>
        </td>
      </tr>

        <tr v-for="t in selectedTables" v-bind:key="t.id">
            <td colspan="2">
                {{t.title}}
            </td>
        </tr>



      <!-- Drei Inputfelder -->

      <tr>
        <td colspan="2">
          <input type="submit" value="Tisch hinzufügen" class="btn" v-on:click="addTable" />
        </td>
      </tr>

      <!-- Standart Buttons -->

      <tr class="submit">
        <td class="sized">
          <input type="submit" value="Zurück" class="btn" v-on:click="onBack" />
        </td>
        <td class="sized">
          <input type="submit" value="Weiter" class="btn" v-on:click="onGetReservation"/>
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
import TableCanvas from '../InputFormComponents/TableCanvas.vue';

import { mapGetters } from 'vuex';

export default {
    name: "StepTwoNew",
    components: {
        TableCanvas,
    },
    data() {
        return {
            selectedTables: [],
            showCanvas: false,
            returnedFromCanvas: undefined
        }
    },
    computed: {
        ...mapGetters(['errormessage', 'step', "allTables"]),
        numberOfSeats() {
          return this.$store.getters.StepOne.numberOfSeats;
        },

    },
    watch: {
        returnedFromCanvas: function(newValue) {
            if(newValue.value !== undefined) {
                this.selectedTables.push(newValue.value);
                this.$store.commit("claimTable", newValue.value);
            }
            this.showCanvas = false;
        }
    },
    methods: {
        addTable() {
            this.showCanvas = true;
        }, onBack() {
          this.$store.commit('setError', '');
          this.$store.commit('decrementStepCounter');
        },
        onGetReservation() {
          var availableSeats = 0;
          var tooMuchTablesForPersons_error = false;
          var tooMuchTablesForPersons_flag = false;

          for (var n of this.selectedTables) {
            if (tooMuchTablesForPersons_flag) tooMuchTablesForPersons_error = true;

            var nosOnTable = n.seats == '' ? 0 : parseInt(n.seats);
            availableSeats += nosOnTable;

            if (availableSeats >= this.numberOfSeats) tooMuchTablesForPersons_flag = true;
          }

          if (availableSeats < this.numberOfSeats) {
            this.$store.commit('setError', 'Zu wenig Tische für alle Gäste ausgewählt!');
          } else if (tooMuchTablesForPersons_error) {
            this.$store.commit('setError', 'Du hast zu viele Tische ausgewählt!');
          } else {

            var selectedIds = [];
            for(var i of this.selectedTables) {
              selectedIds.push(i.id);
            }

            this.$store.commit('setStepTwo', {
              "selectedTableIds": selectedIds
            });
            this.$store.commit('incrementStepCounter');
            this.$store.commit('setError', '');
          }
        }
    }

}

</script>


