<template>
<div>
    <TableCanvas ></TableCanvas>



    
    <!-- <table v-show="step == 2">
      <tr>
        <td colspan="2">
          <h2>
            Verfügbare Tische
            <a>*</a>
          </h2>
        </td>
      </tr>

        <tr v-for="t in selectedTables" v-bind:key="t.id">
            <td v-if="t.seats > 1">
                {{t.title}} ({{t.seats}} Plätze)
            </td>
            <td v-if="t.seats == 1">
                {{t.title}} ({{t.seats}} Platz)
            </td>
            <td>
              <span class="icon" @click="$store.commit('freeTable', t)">
               <font-awesome-icon :icon="['fas', 'minus']" flip="horizontal" size="1x" />
              </span>
            </td>
        </tr>


      <tr>
        <td colspan="2">
          <input type="submit" value="Tisch hinzufügen" class="btn" v-on:click="addTable" />
        </td>
      </tr>-->

      <!-- Standart Buttons -->

      <!--<tr class="submit">
        <td class="sized">
          <input type="submit" value="Zurück" class="btn" v-on:click="onBack" />
        </td>
        <td class="sized">
          <input type="submit" value="Weiter" class="btn" v-on:click="onGetReservation"/>
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <div class="centered" v-if="errormessage != ''">
            <h3 v-html="errormessage"></h3>
          </div>
        </td>
      </tr>
    </table> -->
  </div>
</template>


<script>
import TableCanvas from '../InputFormComponents/TableCanvas.vue';

export default {
    name: "StepTwoNew",
    components: {
        TableCanvas,
    },
}

</script>

<style scoped>

  .icon {
    width: 20%;
    float: right;
  }

  .icon:hover {
    background-color: #da3743;
    color: #ffffff;
  }
</style>


