<template>
    <form>

        <input type="text" name="name" :placeholder="msg">
    </form>
    
</template>

<script>
export default {
    name: "InputFormPersons",
    props: {
        msg: String
    }
}
</script>

<style>


</style>


