<template>
  <div class="centered">
    <table>
      <tr>
        <td colspan="2">
          <h2>Vielen Dank für deine Reservation.</h2>
        </td>
      </tr>
    </table>
  </div>
</template>

<script>

import { mapGetters } from "vuex";

export default {
    name: "StepFour",
    computed: {
        ...mapGetters(["errormessage", "step", "waitingForAjaxResponse"])
    }
};
</script>

<style scoped>
</style>