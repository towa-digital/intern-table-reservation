<template>
  <div class="wrapper">
    <!-- Step 1 -->

    <StepOne />

    <!--Step 2 -->

    <StepTwoMobile v-show="$mq === 'mobile' || $mq === 'tablet'"/>
    <StepTwoGraphic v-show="$mq === 'laptop' || $mq === 'lg'" />

    <!-- Step 3 -->

    <StepThree />

    <!-- Completed -->

    <StepFour />
  </div>
</template> 

<script>

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
  // props: ['reservations'],
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
  margin-bottom: 2%;
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

input,
select,
textarea {
  margin-bottom: 10px !important;
}

input:hover,
select:hover {
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
  height: 30px;;
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
}
</style>

