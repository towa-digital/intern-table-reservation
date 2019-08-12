<template>
  <form>
    <input
      ref="input"
      type="datetime-local"
      name="date"
      placeholder="Datum"
      :value="value"
      @input="updateValue"
    />
  </form>
</template>

<script>
export default {
  name: "InputFormTime",
  props: {
    value: {
      type: String
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

<style scoped>
</style>


