<template>
  <div>
    <input
      ref="input"
      type="email"
      name="email"
      placeholder="E-Mail"
      :value="value"
      @input="updateValue"
    />
  </div>
</template>

<script>
export default {
  name: "InputFormEmail",
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
</style>