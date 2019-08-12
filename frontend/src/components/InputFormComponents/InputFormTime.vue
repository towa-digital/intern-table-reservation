<template>
  <input
    ref="input"
    type="time"
    name="time"
    placeholder="Uhrzeit"
    :value="value"
    @input="updateValue"
  />
</template>

<script>
export default {
  name: "InputFormTime",
  props: {
    value: {
      type: Number
    }
  },
  methods: {
    updateValue(event) {    
      this.$emit("input", this.$refs.input.value);
      event.preventDefault();
    }
  }
};
</script>

<style>
</style>


