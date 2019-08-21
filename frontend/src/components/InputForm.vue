<template>
  <div class="wrapper">
    <!-- Step 1 -->

    <div v-show="reservation.stepOne">
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
            <input type="submit" value="Tisch finden" class="btn" v-on:click="onFindTable" />
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
      <table v-show="reservation.stepTwo">
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
            <InputFormTable ref="tableOne" v-model="reservation.tableOne" />
          </td>
        </tr>

        <!-- Table 2 -->

        <tr>
          <td colspan="2" v-if="inputTwo">
            <InputFormTable ref="tableTwo" v-model="reservation.tableTwo" />
          </td>
        </tr>

        <!-- Table 3 -->

        <tr>
          <td colspan="2" v-if="inputThree">
            <InputFormTable ref="tableThree" v-model="reservation.tableThree" />
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
            <input type="submit" value="Zurück" class="btn" v-on:click="onBackOne" />
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
    <div v-if="!submitstatus">
      <table v-show="reservation.stepThree">
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
          <td class="text">E-Mail</td>
        </tr>
        <tr>
          <td colspan="2">
            <InputFormEmail v-model="reservation.mail" />
          </td>
        </tr>
        <tr class="submit">
          <td class="sized">
            <input type="submit" value="Zurück" class="btn" v-on:click="onBackTwo" />
          </td>
          <td class="sized">
            <input type="submit" value="Fertigstellen" class="btn" v-on:click="onSubmit" />
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

    <div v-if="submitstatus" class="centered">
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
        tableOne: "",
        tableTwo: "",
        tableThree: "",
        tables: [],
        firstname: "",
        lastname: "",
        mail: "",
        date: "",
        phonenumber: "",
        submitted: false,
        stepOne: true,
        stepTwo: false,
        stepThree: false
      },
      inputTwo: false,
      inputThree: false,
      tableOneNumberOfSeats: "",
      tableTwoNumberOfSeats: "",
      tableThreeNumberOfSeats: ""
      // table: this.$store.getters.allTables
    };
  },
  computed: {
    ...mapGetters(["errormessage", "submitstatus"]),
    getAllTables() {
      return this.$store.getters.allTables
    },
    getFreeTables() {
      return this.$store.getters.freeTables;
    }
  },
  methods: {
    ...mapActions(["addReservation"]),
    ...mapActions(["fetchTables"]),
    onSubmit(e) {
      e.preventDefault();

      // Check if there is a Input (Step 3)

      if (
        this.reservation.firstname === "" ||
        this.reservation.lastname === "" ||
        this.reservation.phonenumber === ""
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
        if (that.reservation.tableOne == data.id) {
          that.tableOneNumberOfSeats = data.seats;
        } else if (that.reservation.tableTwo == data.id) {
          that.tableTwoNumberOfSeats = data.seats;
        } else if (that.reservation.tableThree == data.id) {
          that.tableThreeNumberOfSeats = data.seats;
        }
      });

      if (
        (this.tableOneNumberOfSeats === "" ? 0 : parseInt(this.tableOneNumberOfSeats)) +
          (this.tableTwoNumberOfSeats === "" ? 0 : parseInt(this.tableTwoNumberOfSeats)) +
          (this.tableThreeNumberOfSeats === "" ? 0 : parseInt(this.tableThreeNumberOfSeats))<
        this.reservation.numberOfSeats
      ) {
        this.$store.commit("setError", "Zu wenig Tische für alle Gäste ausgewählt!");
      } else {
        this.reservation.stepTwo = false;
        this.reservation.stepThree = true;

        this.$store.commit("setError", "");
        this.reservation.tables = [];
        this.reservation.tables.push(
          this.reservation.tableOne,
          this.reservation.tableTwo,
          this.reservation.tableThree
        );
      }
    },

    // Back-Buttons

    onBackOne() {
      this.$store.commit("setError", "");
      this.reservation.stepTwo = false;
      this.reservation.stepOne = true;
    },
    onBackTwo() {
      this.$store.commit("setError", "");
      this.reservation.stepTwo = true;
      this.reservation.stepThree = false;
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
</style>

