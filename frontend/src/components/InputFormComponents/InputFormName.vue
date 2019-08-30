<template>
  <div>
    <input
      ref="input"
      type="text"
      name="name"
      :placeholder="msg"
      :value="value"
      @input="updateValue"
    />
  </div>
</template>

<script>
export default {
  name: "InputFormPersons",
  props: {
    msg: String,
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


