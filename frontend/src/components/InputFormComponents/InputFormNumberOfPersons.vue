<template>
  <div>
    <input
      ref="input"
      type="number"
      name="number-of-persons"
      placeholder="Personenanzahl"
      :value="value"
      @input="updateValue"
    />
  </div>
</template>

<script>
export default {
  name: "InputFormNumberOfPersons",
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

<style>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
  margin: 0;
}
</style>
