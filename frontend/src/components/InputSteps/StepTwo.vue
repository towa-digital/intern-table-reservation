<template>
  <table>
    <tr v-if="errormessage != ''">
        <td colspan="2">
          <div class="centered">
            <h3 v-html="errormessage"></h3>
          </div>
        </td>
      </tr>
    <tr v-for="t in getCombinations" :key="t.t1">
      <td v-html="getTextForCombination(t)"></td>
      <td><button type="button" class="btn" @click="onClick(t)">Übernehmen</button></td>
    </tr>
    <tr>
      <td colspan="2">
        <input
            type="submit"
            value="Zurück"
            class="btn"
            v-on:click="onBack"
          />
      </td>
    </tr>
  </table>
</template>

<script>
import { mapGetters } from 'vuex';


export default {
  name: 'StepTwo',
  components: {
  },
  data() {
    return {
    };
  },
  methods: {
    onBack() {
      this.$store.commit('setError', '');
      this.$store.commit('decrementStepCounter');
    },
    getTextForCombination(t) {
      var seatsMap = new Map();

      for(var singleTable of t) {
        if(seatsMap.has(singleTable)) {
          var old = parseInt(seatsMap.get(singleTable))
          seatsMap.set(singleTable, old++);
        } else {
          seatsMap.set(singleTable, 1);
        }
      }

      var toReturn = "<ul>";
      for(var entry of seatsMap) {
        if(entry[0] != undefined) toReturn += "<li>"+entry[1]+" x "+entry[0].seats+" Plätze</li>";
      }
      toReturn += "</ul>";

      return toReturn;
    },
    onClick(selectedTables) {
      var availableSeats = 0;
      var tooMuchTablesForPersons_error = false;
      var tooMuchTablesForPersons_flag = false;

      for (n of selectedTables) {
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
            for(var n of selectedTables) {
              this.$store.commit("claimTable", n);
            }

            this.$store.commit('incrementStepCounter');
            this.$store.commit('setError', '');
          }
      }
  },
  computed: {
    ...mapGetters(['allTables', "errormessage"]),
    numberOfSeats() {
      return this.$store.getters.StepOne.numberOfSeats;
    },
    getCombinations() {
      // prüfe, ob ein Wert genau passt
      var suitableTables = [];
      for(var t of this.allTables) {
        if(t.seats >= this.numberOfSeats && t.isFree && !t.isDisabled) suitableTables.push(t);
      }

      if(suitableTables.length != 0) {
        suitableTables.sort(function(a, b) {
          if(a.seats < b.seats) return -1;
          else if(a.seats == b.seats) return 0;
        });

        return [[suitableTables[0]]];
      }

      
      var availableTables = [];
      for(t of this.allTables) {
        if(t.isFree && !t.isDisabled) availableTables.push(t);
      }

      for(var t1 of this.allTables) {
        for(var t2 of this.allTables) {
          if(t1.id == t2.id) continue;

          for(var t3 of this.allTables) {
            if(t3.id == t2.id || t3.id == t1.id) continue;

            if(t1.seats + t2.seats + t3.seats >= this.numberOfSeats) suitableTables.push([t1, t2, t3]);
          }
        }
      }

      return suitableTables;
    },
    
    // numberOfSeats() {
    //     return this.$store.getters.StepOne.numberOfSeats;
    // },
  },
};
</script>

<style scoped>
.btn {
  float: right;
}

ul{
  list-style: none !important;
}
</style>