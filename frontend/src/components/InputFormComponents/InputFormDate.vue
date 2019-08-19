<template>
  <form>
    <input
      ref="date"
      type="date"
      name="date"
      placeholder="Datum"
      @input="updateDate"
      v-on:input="emitToParent"
    />
    <select name="time" ref="time" @change="emitToParent">
      <option v-for="t in timeSlotsForCurrentWeekday" v-bind:key="t.display">{{t.display}}</option>
    </select>
  </form>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "InputFormTime",
  data() {
    return {
      timeSlotsForCurrentWeekday: []
    };
  },
  methods: {
    emitToParent(event) {
        var date = this.$refs.date.value;
        var time = this.$refs.time.value;
        this.$emit("input", (date === "" || time === "" ? "" : date+"T"+time));
    },
    updateDate(event) {
      const val = this.$refs.date.value;

      if(this.$refs.date.value === "") {
        this.timeSlotsForCurrentWeekday = [];

      } else {
        const weekdays = [6, 0, 1, 2, 3, 4, 5];
        var index = weekdays[new Date(val).getDay()];
        this.timeSlotsForCurrentWeekday = this.$store.getters.timeSlots[index];

      }

  },
  },
  created: function() {
    this.$store.dispatch("getTimeSlots");
  }
};
</script>

<style scoped>
</style>


