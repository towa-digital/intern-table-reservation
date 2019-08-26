<template>
  <div class="switch switch-blue">
    <input
      type="radio"
      class="switch-input"
      name="view2"
      value = 0
      id="week2"
      v-model="location"
      @change="updateValue"
      checked
     
    />
    <label for="week2" class="switch-label switch-label-off" >INNEN</label>
    <input
      type="radio"
      class="switch-input"
      name="view2"
      value = 1
      id="month2"
      v-model="location"
      @change="updateValue"
    />
    <label for="month2" class="switch-label switch-label-on">AUSSEN</label>
    <span class="switch-selection"></span>
  </div>
</template>

<script>
export default {
  name: 'InputFormToggle',
  data() {
    return {
      location: "0",
    };
  },
  props: {
    value: {
      type: Number,
    },
  },
  methods: {

    updateValue(event) {
      this.$emit('input', parseInt(this.location));
      event.preventDefault();
    },
  },
};
</script>

<style scoped>
.switch {
  position: relative;
  height: 32px;
  margin: 20px auto;
  background: #f2f2f2;
  border-radius: 3px;

  width: 100%;
}

.switch-label {
  position: relative;
  z-index: 2;
  float: left;
  width: 49%;
  line-height: 32px;
  font-size: 11px;
  color: black;
  text-align: center;
  cursor: pointer;
}
.switch-label:active {
  font-weight: bold;
}

.switch-label-off {
  padding-left: 2px;
}

.switch-label-on {
  padding-right: 2px;
}

.switch-input {
  display: none;
}
.switch-input:checked + .switch-label {
  font-weight: bold;
  color: white;
  text-shadow: 0 1px rgba(255, 255, 255, 0.25);
  -webkit-transition: 0.15s ease-out;
  -moz-transition: 0.15s ease-out;
  -ms-transition: 0.15s ease-out;
  -o-transition: 0.15s ease-out;
  transition: 0.15s ease-out;
  -webkit-transition-property: color, text-shadow;
  -moz-transition-property: color, text-shadow;
  -ms-transition-property: color, text-shadow;
  -o-transition-property: color, text-shadow;
  transition-property: color, text-shadow;
}
.switch-input:checked + .switch-label-on ~ .switch-selection {
  left: 50%;
  /* Note: left: 50%; doesn't transition in WebKit */
}

.switch-selection {
  position: absolute;
  z-index: 1;
  top: 2px;
  left: 2px;
  display: block;
  width: 49%;
  height: 28px;
  border-radius: 3px;
  /* -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.5), 0 0 2px rgba(0, 0, 0, 0.2); */
  /* box-shadow: inset 0 1px rgba(255, 255, 255, 0.5), 0 0 2px rgba(0, 0, 0, 0.2); */
  -webkit-transition: left 0.15s ease-out;
  -moz-transition: left 0.15s ease-out;
  -ms-transition: left 0.15s ease-out;
  -o-transition: left 0.15s ease-out;
  transition: left 0.15s ease-out;
}
.switch-blue .switch-selection {
  background-color: #da3743;
}
</style>