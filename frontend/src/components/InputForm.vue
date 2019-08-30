<template>
  <div class="wrapper">
    <!-- Step 1 -->

    <StepOne v-show="step === 1" />

    <!--Step 2 -->

    <StepTwoMobile v-show="step === 2 && ($mq === 'mobile' || $mq === 'tablet')" />
    <StepTwoGraphic v-show="step === 2 && ($mq === 'laptop' || $mq === 'lg')" />


    <!-- Step 3 -->

    <StepThree v-show="step === 3" />

    <!-- Completed -->

    <StepFour v-if="step === 4" />
  </div>
</template> 

<script>
import { mapGetters } from 'vuex';

import StepOne from './InputSteps/StepOne.vue';
import StepTwoGraphic from './InputSteps/StepTwoNew.vue';
import StepTwoMobile from './InputSteps/StepTwo.vue';
import StepThree from './InputSteps/StepThree.vue';
import StepFour from './InputSteps/StepFour.vue';

export default {
  name: 'InputForm',
  components: {
    StepOne,
    StepTwoGraphic,
    StepTwoMobile,
    StepThree,
    StepFour,
  },
  computed: {
    ...mapGetters(['errormessage', 'step', 'allTables', 'selectedTables']),
  },
  // props: ['reservations'],
};
</script>

<style>

input, select, textarea {
  width: 100%;
  padding-bottom: 10px;
  padding-top: 10px;
  margin-bottom: 15px !important;
  -webkit-appearance: none;

}

input:hover,
select:hover,
textarea:hover {
  margin-bottom: 13.5px !important;
  border-bottom: 2px solid #da3743;
}

input {
  border: none;
  border-bottom: 0.5px solid lightgrey;
  font-size: 15px;

}

select {
  border-left: none;
  border-top: none;
  border-right: none;
  border-bottom: 0.5px solid lightgrey;

  font-size: 15px;
  padding-top: 0;
}


.btn {
  margin-top: 3%;
  padding: 8px;
  border: none;
  background: #da3743;
  color: #fff;
  cursor: pointer;
  margin-bottom: 0 !important;
  -webkit-appearance: none;
  border-radius: 0;
}

.btn:hover {
  background: #e15b64;
  border: none;
  margin-bottom: 0 !important;
}

table {
    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
          box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  padding: 2%;
}

table {
  width: 25%;
  margin-left: 35%;
}

td.formElem {
  padding-top: 7.5px;
}

@media only screen and (max-width: 1200px) {
  table {
    width: 50%;
    margin-left: 25%;
  }
}

@media only screen and (max-width: 900px) {
  table {
    width: 100%;
    margin-left: 0px;
    box-shadow: none;
  }
}

h2 {
  text-align: center;

  padding-bottom: 3%;

  border-bottom: 0.5px solid lightgrey;
}

.centered table {
  border: 2px solid #da3743;
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
  border-top: 16px solid #da3743;
  width: 30px;
  height: 30px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;

  margin: auto;
}

/* Safari */
@-webkit-keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

input[type='submit']:disabled {
  background: #e15b64;
  border: none;
  margin-bottom: 0 !important;

  -webkit-appearance: none;
}
</style>

