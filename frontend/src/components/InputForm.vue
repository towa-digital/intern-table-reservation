<template>
  <div class="wrapper">
    <!-- Step 1 -->

    <div v-show="step == 1">
      <table>
        <tr>
          <td colspan="2">
            <h2>Tisch reservieren</h2>
          </td>
        </tr>
        <tr class="text">
          <td colspan="2">
            Personenanzahl
            <a>*</a>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <InputFormNumberOfPersons v-model="reservation.numberOfSeats" />
          </td>
        </tr>
        <tr class="text">
          <td colspan="2">
            Datum
            <a>*</a>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <InputFormDate v-model="reservation.date" @input="makeTimestamp" />
          </td>
        </tr>
        <tr class="submit">
          <td colspan="2">
            <input type="submit" value="Tisch finden" class="btn" v-on:click="onFindTable" :disabled="waitingForAjaxResponse" />
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

    <!--Step 2 -->

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
            <InputFormTable ref="tableOne" v-model="reservation.tables[0]" />
          </td>
        </tr>

        <!-- Table 2 -->

        <tr>
          <td colspan="2" v-if="inputTwo">
            <InputFormTable ref="tableTwo" v-model="reservation.tables[1]" />
          </td>
        </tr>

        <!-- Table 3 -->

        <tr>
          <td colspan="2" v-if="inputThree">
            <InputFormTable ref="tableThree" v-model="reservation.tables[2]" />
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

    <!-- Step 3 -->
    <div>
      <table v-show="step == 3">
        <tr>
          <td colspan="2">
            <h2>Kontaktdaten</h2>
          </td>
        </tr>
        <tr>
          <td>
            Vorname
            <a>*</a>
          </td>
          <td>
            Nachname
            <a>*</a>
          </td>
        </tr>
        <tr>
          <td>
            <InputFormPersons msg="Max" title="firstname" v-model="reservation.firstname" />
          </td>
          <td>
            <InputFormPersons msg="Mustermann" title="lastname" v-model="reservation.lastname" />
          </td>
        </tr>
        <tr class="text">
          <td>
            Telefonnummer
            <a>*</a>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <InputFormPhonenumber v-model="reservation.phonenumber" />
          </td>
        </tr>
        <tr>
          <td class="text">E-Mail <a>*</a></td>
        </tr>
        <tr>
          <td colspan="2">
            <InputFormEmail v-model="reservation.mail" />
          </td>
        </tr>
        <tr class="submit">
          <td class="sized">
            <input type="submit" value="Zurück" class="btn" v-on:click="onBack" :disabled="waitingForAjaxResponse"/>
          </td>
          <td class="sized">
            <input type="submit" value="Fertigstellen" class="btn" v-on:click="onSubmit" :disabled="waitingForAjaxResponse"/>
          </td>
        </tr>
        <tr>
          <td>
            <!--<div class="g-recaptcha" data-sitekey="6LeETbQUAAAAAA9y89Ol2QQRqcTV3GbbCX5ASLSM"></div>-->
          </td>
        </tr>
        <tr v-if="errormessage != ''">
          <td colspan="2">
            <div class="centered">
              <h3 v-html="errormessage"></h3>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <!-- Completed -->

    <div v-if="step == 4" class="centered">
      <table>
        <tr>
          <td colspan="2">
            <h2>Vielen Dank für deine Reservation.</h2>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template> 

<script>
import { mapGetters, mapActions } from "vuex";

import InputFormPersons from "./InputFormComponents/InputFormName";
import InputFormNumberOfPersons from "./InputFormComponents/InputFormNumberOfPersons";
import InputFormPhonenumber from "./InputFormComponents/InputFormPhonenumber";
import InputFormEmail from "./InputFormComponents/InputFormEmail";
import InputFormDate from "./InputFormComponents/InputFormDate";
import InputFormTable from "./InputFormComponents/InputFormTable";

export default {
  name: "InputForm",
  components: {
    InputFormPersons,
    InputFormNumberOfPersons,
    InputFormPhonenumber,
    InputFormEmail,
    InputFormDate,
    InputFormTable
  },
  props: ["reservations"],
  data() {
    return {
      reservation: {
        from: "",
        numberOfSeats: "",
        tables: [],
        firstname: "",
        lastname: "",
        mail: "",
        date: "",
        phonenumber: "",
      },
      inputTwo: false,
      inputThree: false,
      tablesNumberOfSeats: [],
    };
  },
  computed: {
    ...mapGetters(["errormessage", "step", "waitingForAjaxResponse"]),
    getAllTables() {
      return this.$store.getters.allTables
    },
    getFreeTables() {
      return this.$store.getters.freeTables;
    },

  },
  watch: {
    step(newValue, oldValue) {
      if(newValue === 2 && oldValue === 1) {
        this.$refs.tableOne.onFreeTablesReload();
        if(this.$refs.tableTwo !== undefined) this.$refs.tableTwo.onFreeTablesReload();
        if(this.$refs.tableThree !== undefined) this.$refs.tableThree.onFreeTablesReload();
      }
    }
  },
  mounted() {
    let recaptchaScript = document.createElement('script')
    recaptchaScript.setAttribute('src', 'https://www.google.com/recaptcha/api.js')
    document.head.appendChild(recaptchaScript)
  },
  methods: {
    ...mapActions(["addReservation"]),
    ...mapActions(["fetchTables"]),
    onSubmit(e) {
      e.preventDefault();
      console.log(document.getElementsByName("g-recaptcha-response"));
      // Check if there is a Input (Step 3)

      if (
        this.reservation.firstname == "" ||
        this.reservation.lastname == "" ||
        this.reservation.mail == "" ||
        this.reservation.phonenumber == ""
      ) {
        this.$store.commit("setError", "Bitte fülle alle Pflichtfelder (mit <a>*</a> markiert) aus.");
      } else {
        this.$store.commit("setError", "");
        this.addReservation({
          reservation: this.reservation
        });
      }
    },
    makeTimestamp() {
      var d = new Date(this.reservation.date).getTime();
      const timestamp = Math.floor(d / 1000);

      this.reservation.from = timestamp;
      return timestamp;
    },

    // Switch between Steps

    onFindTable() {
      if (this.reservation.date === "") {
        this.$store.commit("setError", "Bitte geben Sie ein gültiges Datum an.");
      } else if (this.reservation.numberOfSeats === "") {
        this.$store.commit("setError", "Bitte geben Sie die Anzahl an Personen an.");
      } else if (parseInt(this.reservation.numberOfSeats) != this.reservation.numberOfSeats || this.reservation.numberOfSeats <= 0) {
        this.$store.commit("setError", "Die Anzahl der Personen muss eine ganzzahlige Zahl größer als 0 sein.");
      } else {
        this.$store.commit("setError", "");
        this.fetchTables({
          reservation: this.reservation
        });
      }
    },
    onGetReservation() {

      let that = this

      this.getAllTables.forEach(function(data) {
        if (that.reservation.tables[0] == data.id) {
          that.tablesNumberOfSeats[0] = data.seats;
        } else if (that.reservation.tables[1] == data.id) {
          that.tablesNumberOfSeats[1] = data.seats;
        } else if (that.reservation.tables[2] == data.id) {
          that.tablesNumberOfSeats[2] = data.seats;
        }
      });

      var availableSeats = 0;
      var tooMuchTablesForPersons_error = false;
      var tooMuchTablesForPersons_flag = false;

      for(var n of this.tablesNumberOfSeats) {
        if(tooMuchTablesForPersons_flag) tooMuchTablesForPersons_error = true;

          var nosOnTable = (n == "" ? 0 : parseInt(n));
          availableSeats += nosOnTable;

          if(availableSeats >= this.reservation.numberOfSeats) tooMuchTablesForPersons_flag = true;
      }

      if (availableSeats < this.reservation.numberOfSeats) {
        this.$store.commit("setError", "Zu wenig Tische für alle Gäste ausgewählt!");
      } else if (tooMuchTablesForPersons_error) {
        this.$store.commit("setError", "Du hast zu viele Tische ausgewählt!");
      } else {
        this.$store.commit("incrementStepCounter");

        this.$store.commit("setError", "");
      }
    },

    // Back-Buttons

    onBack() {
      this.$store.commit("setError", "");
      this.$store.commit("decrementStepCounter");
    },


    // Switch number of Inputs (Step 2)

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
    }
  }
};
</script>

<style>
input {
  border: none;
  border-bottom: 0.5px solid lightgrey;
  width: 100%;
  font-size: 15px;
  padding-bottom: 3%;
  padding-top: 2%;
}

select {
  width: 100%;

  border-left: none;
  border-top: none;
  border-right: none;
  border-bottom: 0.5px solid lightgrey;

  font-size: 15px;

  padding-bottom: 4.9%;
  padding-top: 2%;

}

input, select {
  margin-bottom: 10px !important;

}

input:hover, select:hover {
  margin-bottom: 8.5px !important;
  border-bottom: 2px solid #da3743;
}

.btn {
  margin-top: 3%;
  padding: 8px;
  border: none;
  background: #da3743;
  color: #fff;
  cursor: pointer;
  margin-bottom: 0 !important;
}

.btn:hover {
  background: #e15b64;
  border: none;
  margin-bottom: 0 !important;
}

table {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  padding: 2%;
}

table {
  width: 25%;
  margin-left: 35%;
}

h2 {
  text-align: center;

  padding-bottom: 3%;

  border-bottom: 0.5px solid lightgrey;
}






.centered table {
  border: 2px solid #da3743;
}

.text {
  margin-top: 20px;
}

a {
  color: #da3743;
}

.sized {
  width: 50%;
}

.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

input[type="submit"]:disabled {
  background: #e15b64;
  border: none;
  margin-bottom: 0 !important;
}
</style>

