<template>
  <form class="quantity">
    <input type="number" name="number-of-persons" placeholder="Personenanzahl" />
  </form>
</template>

<script>
export default {
  name: "InputFormNumberOfPersons"
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
