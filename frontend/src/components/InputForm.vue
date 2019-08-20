<template>
  <div class="wrapper">
    <!-- Step 1 -->

    <div v-if="reservation.stepOne">
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
            <div class="centered" v-if="reservation.error">
              <h3>{{ reservation.errormessage }}</h3>
            </div>
            <div class="centered" v-if="errorstatus">
              <h3>{{ errormessage }}</h3>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <!--Step 2 -->

    <div>
      <table v-if="reservation.stepTwo">
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
            <InputFormTable v-model="reservation.tableOne" />
          </td>
        </tr>

        <!-- Table 2 -->

        <tr>
          <td colspan="2" v-if="inputTwo">
            <InputFormTable v-model="reservation.tableTwo" />
          </td>
        </tr>

        <!-- Table 3 -->

        <tr>
          <td colspan="2" v-if="inputThree">
            <InputFormTable v-model="reservation.tableThree" />
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
          <td id="sized">
            <input type="submit" value="Tisch hinzufügen" class="btn" v-on:click="addInputThree" />
          </td>
          <td id="sized">
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
          <td>
            <input type="submit" value="Zurück" class="btn" v-on:click="onBackOne" />
          </td>
          <td>
            <input type="submit" value="Weiter" class="btn" v-on:click="onGetReservation" />
          </td>
        </tr>

        <tr v-if="this.toFewTables">
          <td colspan="2"> 
            <h3>Sie haben zu wenig Sitze für Ihre Gruppe</h3>
          </td>
        </tr>
      </table>
    </div>

    <!-- Step 3 -->
    <div v-if="!submitstatus">
      <table v-if="reservation.stepThree">
        <tr>
          <td colspan="2">
            <h2>Kontaktdaten</h2>
          </td>
        </tr>
        <tr>
          <td>
            Vorame
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
          <td>
            <input type="submit" value="Zurück" class="btn" v-on:click="onBackTwo" />
          </td>
          <td>
            <input type="submit" value="Fertigstellen" class="btn" v-on:click="onSubmit" />
          </td>
        </tr>
        <tr v-if="inputErrorStepThree">
          <td colspan="2">
            <h3>
              Alle Felder mit
              <a>*</a> müssen ausgefüllt werden
            </h3>
          </td>
        </tr>
      </table>
    </div>

    <!-- Completed -->

    <div v-if="submitstatus" class="centered">
      <table>
        <tr>
          <td colspan="2">
            <h2>Vielen Dank für deine Reservation</h2>
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
        errormessage: "",
        error: false,
        stepOne: true,
        stepTwo: false,
        stepThree: false
      },
      inputTwo: false,
      inputThree: false,
      inputErrorStepThree: false,
      toFewTables: false,
      tableOneNumberOfSeats: "",
      tableTwoNumberOfSeats: "",
      tableThreeNumberOfSeats: ""
      // table: this.$store.getters.allTables
    };
  },
  computed: {
    ...mapGetters(["errormessage", "errorstatus", "submitstatus"]),
    getAllTables() {
      return this.$store.getters.allTables
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
        this.inputErrorStepThree = true;
      } else {
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
      // Check if there is a Input (Step 1)

      if (this.reservation.date === "") {
        this.reservation.error = true;
        this.reservation.errormessage = "Bitte geben Sie ein Datum an!";
      } else if (this.reservation.numberOfSeats === "") {
        this.reservation.error = true;
        this.reservation.errormessage =
          "Bitte geben Sie eine Peronenanzahl an!";
      } else {
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
        this.tableOneNumberOfSeats +
          this.tableTwoNumberOfSeats +
          this.tableThreeNumberOfSeats <
        this.reservation.numberOfSeats
      ) {
        this.toFewTables = true;
      } else {
        this.reservation.stepTwo = false;
        this.reservation.stepThree = true;

        this.reservation.tables.push(
          this.reservation.tableOne,
          this.reservation.tableTwo,
          this.reservation.tableThree
        );
      }
    },

    // Back-Buttons

    onBackOne() {
      this.reservation.error = false;

      this.reservation.stepTwo = false;
      this.reservation.stepOne = true;
    },
    onBackTwo() {
      this.reservation.error = false;

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
  padding: 3%;
  margin-top: 3%;
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

#sized {
  width: 50%;
}
</style>

