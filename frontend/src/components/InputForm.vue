<template>
  <div class="wrapper">
    <table v-if="!submitstatus">
      <tr>
        <td colspan="2">
          <h2>Platzreservierung</h2>
        </td>
      </tr>
      <tr class="text">
        <td>Personenanzahl</td>
        <td>Tisch</td>
      </tr>
      <tr>
        <td>
          <InputFormNumberOfPersons v-model="reservation.numberOfSeats" />
        </td>
        <td>
          <InputFormTable v-model="reservation.tables" />
        </td>
      </tr>
      <tr>
        <td>Vorame</td>
        <td>Nachname</td>
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
        <td>Telefonnummer</td>
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
      <tr class="text">
        <td>Datum</td>
      </tr>
      <tr>
        <td colspan="2">
          <InputFormDate v-model="reservation.date" @input="makeTimestamp" />
        </td>
      </tr>
      <tr class="submit">
        <td colspan="2">
          <input type="submit" value="Tisch finden" class="btn" v-on:click="onSubmit" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <div  class="centered" v-if="errorstatus">
            <h3>{{ errormessage }}</h3>
          </div>
        </td>
      </tr>
    </table>

    <div v-if="submitstatus" class="centered">
      <h3>Vielen Dank für deine Reservierung</h3>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
<<<<<<< HEAD

const axios = require("axios");
=======
>>>>>>> db1e8d5f248167895f6293b61b9672f31920a6d3

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
        tables: "",
        firstname: "",
        lastname: "",
        mail: "",
        date: "",
        phonenumber: "",
        submitted: false,
        errormessage: "",
        error: false
      }
    };
  },
  computed: mapGetters(["errormessage", "errorstatus", "submitstatus"]),
  methods: {
    ...mapActions(['addReservation']),
    onSubmit(e) {
      e.preventDefault();
      this.addReservation({
        reservation: this.reservation
      })
    },
    makeTimestamp() {
      var d = new Date(this.reservation.date).getTime();
      const timestamp = Math.floor(d / 1000);

      this.reservation.from = timestamp

<<<<<<< HEAD

      console.log(timestamp)

=======
>>>>>>> db1e8d5f248167895f6293b61b9672f31920a6d3
      return timestamp;
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

  margin-bottom: 3%;
}

input:hover {
  border-bottom: 2px solid #da3743;
}

.btn:hover {
  background: #e15b64;
  border: none;
}

table {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  padding: 4%;
}

.wrapper {
  width: 30%;
  margin-left: 35%;
}

h2 {
  text-align: center;

  padding-bottom: 3%;

  border-bottom: 0.5px solid lightgrey;
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

  margin-bottom: 3%;
}

select:hover {
  border-bottom: 2px solid #da3743;
}

.btn {
  padding: 3%;
  margin-top: 3%;
  border: none;
  background: #da3743;
  color: #fff;
  cursor: pointer;
}

.centered {
  text-align: center;
}
</style>


