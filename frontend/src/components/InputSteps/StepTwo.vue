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
      <td>
        <button type="button" class="btn" @click="onClick(t)">Übernehmen</button>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="submit" value="Zurück" class="btn" v-on:click="onBack" />
      </td>
    </tr>
  </table>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'StepTwo',
  components: {},
  data() {
    return {
      errormessage: "",
    };
  },
  methods: {
    onBack() {
      this.$store.commit('decrementStepCounter');
      this.errormessage = "";
    },
    getTextForCombination(t) {
      console.log("TEXT OF:");
      console.log(t);
      var seatsMap = new Map();

      for (var singleTable of t) {
        if (seatsMap.has(singleTable)) {
          var old = parseInt(seatsMap.get(singleTable));
          seatsMap.set(singleTable, old++);
        } else {
          seatsMap.set(singleTable, 1);
        }
      }

      var toReturn = '<ul>';
      for (var entry of seatsMap) {
        if (entry[0] != undefined) toReturn += '<li>' + entry[1] + ' x ' + entry[0].seats + ' Plätze</li>';
      }
      toReturn += '</ul>';

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
        this.errormessage = 'Zu wenig Tische für alle Gäste ausgewählt!';
      } else if (tooMuchTablesForPersons_error) {
        this.errormessage = 'Du hast zu viele Tische ausgewählt!';
      } else {
        for (var n of selectedTables) {
          this.$store.commit('claimTableAutomatically', n);
        }

        this.$store.commit('incrementStepCounter');
        this.errormessage = "";
      }
    },
    /**
     * Diese Funktion nimmt zwei Arrays aus Tisch-Objekten als Parameter und überprüft, ob 
     * die Anzahl an Sitzplätzen für alle Elemente in den Arrays übereinstimmt.
     * 
     * Hierbei soll die Reihenfolge nicht beachtet werden.
     * 
     * Die Arrays können undefined enthalten.
     */
    areTableSeatsEqual(arr1, arr2) {
      /**
       * Die Zahl 31 hat keine besondere Bedeutung, die Multiplikation/Addition mit einer Konstanten dient nur 
       * dazu, dass z.B. zwei Tische mit je einem Sitzplatz nicht denselben Wert ergeben wie ein Tisch mit zwei
       * Sitzplätzen.
       */
      const factor = 31;

      var arr1Val = 0;
      for(var e of arr1) {
        if(e === undefined) continue;
        arr1Val += factor * (e.seats + factor);
      }

      var arr2Val = 0;
      for(var e of arr2) {
        if(e === undefined) continue;
        arr2Val += factor * (e.seats + factor);
      }

      return arr1Val === arr2Val;
    }
  },
  computed: {
    ...mapGetters(['allTables', 'step']),
    numberOfSeats() {
      return this.$store.getters.StepOne.numberOfSeats;
    },
    getCombinations() {
      // die Berechnungen sollen nicht ausgeführt werden, wenn der zweite Schritt gar nicht aktiv ist
      if(this.step != 2) return [];

      // prüfe, ob ein Wert genau passt
      var suitableTables = [];
      for (var t of this.allTables) {
        console.log(this.$store);
        console.log(t.seats + " " + this.numberOfSeats);
        if (t.seats >= this.numberOfSeats && t.isFree && !t.isDisabled) suitableTables.push({
            "seatsSum": t.seats,
            "tables": [t]
          });
      }

      console.log("all tables:");
      console.log(this.allTables);
      console.log("suitable tables");
      console.log(suitableTables);

      if(suitableTables.length == 0) {
        // kein Tisch passt direkt:
        // Kombinationen müssen gefunden werden
        var availableTables = [];
        for (t of this.allTables) {
          if (t.isFree && !t.isDisabled) availableTables.push(t);
        }

        for (var t1 of availableTables) {
          for (var t2 of availableTables) {
            if (t1.id == t2.id) continue;
            
            // prüfe, ob bereits mit zwei Tischen eine passende Kombination gefunden wird
            var sum_t1_t2 = parseInt(t1.seats) + parseInt(t2.seats);
            if(sum_t1_t2 >= this.numberOfSeats && sum_t1_t2 <= this.numberOfSeats + this.$store.getters.options.maxUnusedSeatsPerReservation) {
                // diese Tischkombination aus zwei Tischen reicht aus
                var combination = {
                  "seatsSum": sum_t1_t2,
                  "tables": [t1, t2]
                };


                // nun müssen wir prüfen, ob diese Tischkombination bereis eingefügt wurde
                var canInsert = true;
                for(var alreadyExistingCombination of suitableTables) {
                   
                  if(this.areTableSeatsEqual(alreadyExistingCombination.tables, combination.tables)) {
                    
                    // diese Kombination existiert bereits
                    canInsert = false;
                  }  
                }

                if(canInsert) {
                    suitableTables.push(combination);
                }
            } else {
              for (var t3 of availableTables) {
                if (t3.id == t2.id) continue;
                var sum_t1_t2_t3 = sum_t1_t2 + parseInt(t3.seats);

                // prüfe, ob die Kombination mit drei Tischen passt
                if (sum_t1_t2_t3 >= this.numberOfSeats && sum_t1_t2_t3 <= this.numberOfSeats + this.$store.getters.options.maxUnusedSeatsPerReservation) {
                  // sie passt
                  var combination = {
                    "seatsSum": sum_t1_t2_t3,
                    "tables": [t1, t2, t3]
                  };

                  // nun müssen wir prüfen, ob diese Tischkombination bereits eingefügt wurde
                  var canInsert = true;
                  for(var alreadyExistingCombination of suitableTables) {

                    if(this.areTableSeatsEqual(alreadyExistingCombination.tables, combination.tables)) {
                      
                      // diese Kombination existiert bereits
                      canInsert = false;
                    }  
                  }
                  if(canInsert) {
                      suitableTables.push(combination);
                  }
                }
              }
            }
          }
        }
      }

      if(suitableTables.length == 0) {
        // es wurde kein passender einzelner Tisch und keine passende Kombination gefunden
        this.errormessage = 'Kein Tisch verfügbar!';
        return [];
      }

      // sortieren nach Anzahl Sitzplätze
      suitableTables.sort(function(a, b) {
          if (a.seatsSum < b.seatsSum) return -1;
          else if (a.seatsSum == b.seatsSum) return 0;
      });

      // falls ein einzelner Tisch die passendste Kombination ist, nur diesen zurückgegben
      if(suitableTables[0].tables.length == 1) {
        return [[suitableTables[0].tables[0]]];
      }
      
      // ansonsten maximal 4 Kombinationen zurückgeben
      var combinationsToReturn = [];
      for(var c of suitableTables) {
        // Kombinationen, welche mehr als zwei Sitzplätze mehr freilassen als die beste Kombination, nicht anzeigen
        if(c.seatsSum <= suitableTables[0].seatsSum + 2 && combinationsToReturn.length < 4) {
          /**
           * Falls eine Reservierung für z.B. 5 Personen aufgegeben wird, könnten wir in den Kombinationen beispielsweise Tische mit
           * 2 und 3 Plätzen haben und zusätzlich eine Kombination mit 2, 3 und 2 Plätzen haben. Da allerdings schon die kleinere
           * ausreicht, ist es nicht notwendig, auch die Kombination mit 3 Tischen anzuzeigen.
           * 
           * Bei einem Tisch ist das kein Problem, da in diesem Fall sowieso nur die Kombination mit einem Tisch angezeigt wird.
           */
          var s0 = (c.tables[0] === undefined) ? Number.NEGATIVE_INFINITY : parseInt(c.tables[0].seats);
          var s1 = (c.tables[1] === undefined) ? Number.NEGATIVE_INFINITY : parseInt(c.tables[1].seats);
          var s2 = (c.tables[2] === undefined) ? Number.NEGATIVE_INFINITY : parseInt(c.tables[2].seats);
          if(! (((s0 + s1 >= this.numberOfSeats && s2 != Number.NEGATIVE_INFINITY) ||
              (s1 + s2 >= this.numberOfSeats && s0 != Number.NEGATIVE_INFINITY) ||
              (s0 + s2 >= this.numberOfSeats && s1 != Number.NEGATIVE_INFINITY)))) {
            combinationsToReturn.push(c.tables);

          }
        } else break;
      }

      return combinationsToReturn;
    },

    // return suitableTables;
  },
};
</script>

<style scoped>
.btn {
  float: right;
}

ul {
  list-style: none !important;
}
</style>