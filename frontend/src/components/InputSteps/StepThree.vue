<template>
  <div>
    <table>
      <tr>
        <td colspan="2">
          <h2>Kontaktdaten</h2>
        </td>
      </tr>
      <tr>
        <td>
          Vorname
          <a>*</a>
          <InputFormPersons msg="Max" title="firstname" v-model="firstname" />
        </td>
        <td>
          Nachname
          <a>*</a>
          <InputFormPersons msg="Mustermann" title="lastname" v-model="lastname" />
        </td>
      </tr>
      <tr class="text">
        <td class="formElem" colspan="2">
          Telefonnummer
          <a>*</a>
          <InputFormPhonenumber v-model="phonenumber" />
        </td>
      </tr>
      <tr class="text">
        <td class="formElem" colspan="2">
          E-Mail
          <a>*</a>
          <InputFormEmail v-model="mail" />
        </td>
      </tr>
      <tr class="text">
        <td class="formElem" colspan="2">Anmerkungen
          <InputFormTextarea v-model="remarks" />
        </td>
      </tr>
      <tr class="submit">
        <td class="sized">
          <input
            type="submit"
            value="Zurück"
            class="btn"
            v-on:click="onBack"
            :disabled="waitingForAjaxResponse"
          />
        </td>
        <td class="sized">
          <input
            type="submit"
            value="Fertigstellen"
            class="btn"
            v-on:click="onSubmit"
            :disabled="waitingForAjaxResponse"
          />
        </td>
      </tr>
      <tr>
        <td>
          <!--<div class="g-recaptcha" data-sitekey="6LeETbQUAAAAAA9y89Ol2QQRqcTV3GbbCX5ASLSM"></div>-->
        </td>
      </tr>
      <tr>
        <td colspan="2"  v-if="waitingForAjaxResponse">
          <div class="loader">

          </div>
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
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

import InputFormPersons from './../InputFormComponents/InputFormName';
import InputFormPhonenumber from './../InputFormComponents/InputFormPhonenumber';
import InputFormEmail from './../InputFormComponents/InputFormEmail';
import InputFormTextarea from './../InputFormComponents/InputFormTextarea';

export default {
  name: 'StepThree',
  components: {
    InputFormPersons,
    InputFormPhonenumber,
    InputFormEmail,
    InputFormTextarea,
  },
  data() {
    return {
      firstname: '',
      lastname: '',
      mail: '',
      phonenumber: '',
      remarks: ''
    };
  },
  methods: {
    ...mapActions(['addReservation']),
    onSubmit(e) {
      e.preventDefault();
      // Check if there is a Input (Step 3)

      if (this.firstname == '' || this.lastname == '' || this.mail == '' || this.phonenumber == '') {
        this.$store.commit('setError', 'Bitte fülle alle Pflichtfelder (mit <a>*</a> markiert) aus.');
      } else {
        this.$store.commit('setStepThree', this);
        this.$store.commit('setError', '');
        this.addReservation();
      }
    },
    onBack() {
      this.$store.commit('setError', '');
      this.$store.commit('decrementStepCounter');
    },
  },
  computed: {
    ...mapGetters(['errormessage', 'step', 'waitingForAjaxResponse']),
  },
};
</script>

<style scoped>

</style>