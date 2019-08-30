<template>
  <div>
    <textarea
      rows="1"
      cols="80"
      :value="value"
      @input="updateValue"
      ref="input"
      placeholder="Anlässe, Wünsche usw."
    ></textarea>
  </div>
</template>

<script>
export default {
  name: 'InputFormPhonenumber',
  props: {
    value: {
      type: String,
    },
  },
  methods: {
    updateValue(event) {
      this.$emit('input', this.$refs.input.value);
      event.preventDefault();
    },
  },
};
</script>

<style scoped>
textarea {
  width: 100%;
  border: none;
  border-bottom: 0.5px solid lightgrey;
  font-size: 15.4px;
  padding-bottom: 3%;
  padding-top: 2%;
  font-family: arial;
}

textarea:hover {
  border-bottom: 2px solid #da3743;
}
</style>