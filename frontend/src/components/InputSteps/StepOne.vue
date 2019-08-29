<template>
  <div>
    <table>
      <tr>
        <td colspan="2">
          <h2>Tisch reservieren</h2>
        </td>
      </tr>
      <tr class="text">
        <td colspan="2">
          <span class="inputLabel">Personenanzahl <a>*</a></span>
          <InputFormNumberOfPersons v-model="numberOfSeats" />
        </td>
      </tr>
      <tr class="text">
        <td colspan="2" class="formElem">
          <span class="inputLabel"> Datum <a>*</a></span>
          <InputFormDate v-model="date" @input="makeTimestamp" />
        </td>
      </tr>
      <tr class="text">
        <td colspan="2" class="formElem">
          <span class="inputLabel">Wo soll sich Ihr Tisch befinden?</span>
          <InputFormToggle v-model="location"/>
        </td>
      </tr>

      <tr class="submit">
        <td colspan="2">
          <input
            type="submit"
            value="Tisch finden"
            class="btn"
            v-on:click="onFindTable"
            :disabled="waitingForAjaxResponse"
          />
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
import { mapGetters, mapActions } from 'vuex';

import InputFormNumberOfPersons from './../InputFormComponents/InputFormNumberOfPersons';
import InputFormDate from './../InputFormComponents/InputFormDate';
import InputFormToggle from './../InputFormComponents/InputFormToggle';

export default {
  name: 'StepOne',
  components: {
    InputFormNumberOfPersons,
    InputFormDate,
    InputFormToggle,
  },
  computed: {
    ...mapGetters(['step', 'waitingForAjaxResponse']),
  },
  data() {
    return {
      errormessage: '',
      numberOfSeats: '',
      date: '',
      from: '',
      location: 0
    };
  },
  methods: {
    ...mapActions(['fetchTables']),
    onFindTable() {
      var that = this;

      if (this.date === '') {
        this.errormessage = 'Bitte geben Sie ein gültiges Datum an.';
      } else if (this.numberOfSeats === '') {
        this.errormessage = 'Bitte geben Sie die Anzahl an Personen an.';
      } else if (parseInt(this.numberOfSeats) != this.numberOfSeats || this.numberOfSeats <= 0) {
        this.errormessage = 'Die Anzahl der Personen muss eine ganzzahlige Zahl größer als 0 sein.';
      } else {
        this.errormessage = "";
        this.$store.commit('setStepOne', {
          numberOfSeats: that.numberOfSeats,
          date: that.date,
          from: that.from,
          location: that.location
        });

        this.fetchTables({
          errorCallback: function(msg) {
            that.errormessage = msg;
          },
          time: this,
        });
      }
    },
    makeTimestamp() {
      var d = new Date(this.date).getTime();
      const timestamp = Math.floor(d / 1000);

      this.from = timestamp;
      return timestamp;
    },
  },
};
</script>

<style scoped>
</style>