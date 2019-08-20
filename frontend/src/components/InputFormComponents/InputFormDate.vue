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
    };
  },
  methods: {
    getWeekday(dateObject) {
        const weekdays = [6, 0, 1, 2, 3, 4, 5];
        return weekdays[dateObject.getDay()];
    },
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
        this.timeSlotsForCurrentWeekday = this.$store.getters.timeSlots[this.getWeekday(new Date(val))];
      }

      event.preventDefault();
    }
  },
  created: function() {
    this.$store.dispatch("getTimeSlots");

    // set today's date as default value for the date input field
    this.dateFieldValue = new Date().toJSON().slice(0, 10);
    window.onload = function() {
      document.getElementById("today").value = new Date().toJSON().slice(0, 10);
    }
    
  },
  mounted() {
    // as soon as the opening hours have been loaded, load the one for the current weekday into timeSlotsForCurrentWeekday
    this.$store.watch((state, getters) => getters.timeSlots, (newValue) => {
        this.timeSlotsForCurrentWeekday = newValue[this.getWeekday(new Date())];
         
    })
  },
};
</script>

<style scoped>
</style>


