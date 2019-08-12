<template>
  <div>
    <table>
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
          <InputFormNumberOfPersons v-model="numberofpersons" />
        </td>
        <td>
          <InputFormTable v-model="table" />
        </td>
      </tr>
      <tr>
        <td>Vorame</td>
        <td>Nachname</td>
      </tr>
      <tr>
        <td>
          <InputFormPersons msg="Max" title="firstname" v-model="firstname" />
        </td>
        <td>
          <InputFormPersons msg="Mustermann" title="lastname" v-model="lastname" />
        </td>
      </tr>
      <tr class="text">
        <td>Telefonnummer</td>
      </tr>
      <tr>
        <td colspan="2">
          <InputFormPhonenumber v-model="phonenumber" />
        </td>
      </tr>
      <tr>
        <td class="text">E-Mail</td>
      </tr>
      <tr>
        <td colspan="2">
          <InputFormEmail v-model="email" />
        </td>
      </tr>
      <tr class="text">
        <td>Datum</td>
      </tr>
      <tr>
        <td colspan="2">
          <InputFormDate v-model="date" @input="makeTimestamp" />
        </td>
      </tr>
      <tr class="submit">
        <td colspan="2">
          <input type="submit" value="Tisch finden" class="btn" v-on:click="addReservation" />
        </td>
      </tr>
    </table>
    <div v-bind:key="reservation.id" v-for="reservation in reservations">
      <h3>{{ reservation.name }}</h3>
    </div>
  </div>
</template>

<script>
const axios = require('axios');

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
      numberofpersons: "",
      table: "",
      firstname: "",
      lastname: "",
      email: "",
      phonenumber: "",
      date: ""
    };
  },

  methods: {
    addReservation(timestamp) {

      axios
        .post(
          "http://localhost/wordpress/wp-json/tischverwaltung/v1/savenewreservation",
          {
            from: this.makeTimestamp(),
            tables: this.table,
            firstname: this.firstname,
            lastname: this.lastname,
            phonenumber: this.phonenumber,
            mail: this.email,
            numberOfSeats: this.numberofpersons
          }
        )
        .then(function(response) {
          console.log(response);
        })
        .catch(function(error) {
          if(error.response) alert(error.response.data.message);
        });
    },
    makeTimestamp() {
      var d = new Date(this.date).getTime();
      const timestamp = Math.floor(d / 1000);

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
</style>


