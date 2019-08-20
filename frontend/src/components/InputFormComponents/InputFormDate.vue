<template>
  <form>
    <input
      id="today"
      ref="date"
      type="date"
      name="date"
      
      @input="updateDate"
      v-on:input="emitToParent"
    />
    <select name="time" ref="time" @change="emitToParent">
      <option
        disabled
        selected
        value
        v-if="timeSlotsForCurrentWeekday.length != 0"
      >Bitte Uhrzeit wählen.</option>
      <option
        disabled
        selected
        value
        v-if="dateFieldValue == '' && timeSlotsForCurrentWeekday.length == 0"
      >Bitte erst Datum wählen.</option>
      <option
        disabled
        selected
        value
        v-if="dateFieldValue != '' && timeSlotsForCurrentWeekday.length == 0"
      >nicht geöffnet</option>
      <option v-for="t in timeSlotsForCurrentWeekday" v-bind:key="t.display">{{t.display}}</option>
    </select>
  </form>
</template>

<script>

export default {
  name: "InputFormTime",
  data() {
    return {
      timeSlotsForCurrentWeekday: [],
      dateFieldValue: "",
      today: '',
      currentDate: ''
    };
  },
  methods: {
    emitToParent(event) {
      var date = this.$refs.date.value;
      var time = this.$refs.time.value;
      this.$emit("input", date === "" || time === "" ? "" : date + "T" + time);

      event.preventDefault();
    },
    updateDate(event) {
      const val = this.$refs.date.value;
      this.dateFieldValue = val;

      if (this.$refs.date.value === "") {
        this.timeSlotsForCurrentWeekday = [];
      } else {
        const weekdays = [6, 0, 1, 2, 3, 4, 5];
        var index = weekdays[new Date(val).getDay()];
        this.timeSlotsForCurrentWeekday = this.$store.getters.timeSlots[index];
      }

      event.preventDefault();
    }
  },
  created: function() {
    this.$store.dispatch("getTimeSlots");

    this.currentDate = new Date().toJSON().slice(0,10);
    document.getElementById("today").defaultValue = this.currentDate;
  }
};
</script>

<style scoped>
</style>


