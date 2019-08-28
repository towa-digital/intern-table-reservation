<template>
  <div>
    <table>
      <tr>
        <td colspan="2">
          <h2>Tisch reservieren</h2>
        </td>
      </tr>
      <tr class="text">
        <td colspan="2" class="formElem">
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
    ...mapGetters(['errormessage', 'step', 'waitingForAjaxResponse']),
  },
  data() {
    return {
      numberOfSeats: '',
      date: '',
      from: '',
      location: 0
    };
  },
  methods: {
    ...mapActions(['fetchTables']),
    onFindTable() {
      if (this.date === '') {
        this.$store.commit('setError', 'Bitte geben Sie ein gültiges Datum an.');
      } else if (this.numberOfSeats === '') {
        this.$store.commit('setError', 'Bitte geben Sie die Anzahl an Personen an.');
      } else if (parseInt(this.numberOfSeats) != this.numberOfSeats || this.numberOfSeats <= 0) {
        this.$store.commit('setError', 'Die Anzahl der Personen muss eine ganzzahlige Zahl größer als 0 sein.');
      } else {
        this.$store.commit('setError', '');
        this.$store.commit('setStepOne', this);
        // this.$store.commit("incrementStepCounter")
        this.fetchTables({
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