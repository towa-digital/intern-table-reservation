<template>
    <div>
        <input ref="input" type="tel" name="phone-number" placeholder="Telefonnummer" :value="value" @input="updateValue">
    </div>  
</template>

<script>
export default {
    name:"InputFormPhonenumber",
     props: {
        value: {
            type: String
        }
    },
    methods: {
        updateValue(event) {
            this.$emit('input', this.$refs.input.value);
            event.preventDefault();
        }
    }
}
</script>

<style>

</style>


