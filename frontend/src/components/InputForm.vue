<template>
  <table>
    <tr>
      <td colspan="2">
        <h2>Platzreservierung</h2>
      </td>
    </tr>
    <tr class="text">
      <td>Personenanzahl</td>
      <td>Tisch</td>
    </tr>
    <tr>
      <td>
        <InputFormNumberOfPersons />
      </td>
      <td>
        <InputFormTable />
      </td>
    </tr>
    <tr>
      <td>Vorame</td>
      <td>Nachname</td>
    </tr>
    <tr>
      <td>
        <InputFormName msg="Max" />
      </td>
      <td>
        <InputFormName msg="Mustermann" />
      </td>
    </tr>
    <tr class="text">
      <td>Telefonnummer</td>
    </tr>
    <tr>
      <td colspan="2">
        <InputFormPhonenumber />
      </td>
    </tr>
    <tr>
      <td class="text">E-Mail</td>
    </tr>
    <tr>
      <td colspan="2">
        <InputFormEmail />
      </td>
    </tr>
    <tr class="text">
      <td>Datum</td>
      <td>Uhrzeit</td>
    </tr>
    <tr>
      <td>
        <InputFormDate />
      </td>
      <td>
        <InputFormTime />
      </td>
    </tr>
    <tr class="submit">
      <td colspan="2">
        <InputFormSubmit />
      </td>
    </tr>
  </table>
</template>

<script>
import InputFormName from "./InputFormComponents/InputFormName";
import InputFormNumberOfPersons from "./InputFormComponents/InputFormNumberOfPersons";
import InputFormPhonenumber from "./InputFormComponents/InputFormPhonenumber";
import InputFormEmail from "./InputFormComponents/InputFormEmail";
import InputFormDate from "./InputFormComponents/InputFormDate";
import InputFormTime from "./InputFormComponents/InputFormTime";
import InputFormSubmit from "./InputFormComponents/InputFormSubmit";
import InputFormTable from "./InputFormComponents/InputFormTable";

export default {
  name: "InputForm",
  components: {
    InputFormName,
    InputFormNumberOfPersons,
    InputFormPhonenumber,
    InputFormEmail,
    InputFormDate,
    InputFormTime,
    InputFormSubmit,
    InputFormTable
  }
};
</script>

<style>
input {
  border: none;
  border-bottom: 0.5px solid lightgrey;
  width: 100%;

  font-size: 15px;

  padding-bottom: 3%;
  padding-top: 2%;

  margin-bottom: 3%;
}

input:hover {
  border-bottom: 2px solid #da3743;
}

.btn:hover {
  background: #e15b64;
  border: none;
}

table {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  padding: 4%;
}

h2 {
  text-align: center;

  padding-bottom: 3%;

  border-bottom: 0.5px solid lightgrey;
}

select {
  width: 100%;

  border-left: none;
  border-top: none;
  border-right: none;
  border-bottom: 0.5px solid lightgrey;

  font-size: 15px;

  padding-bottom: 4.9%;
  padding-top: 2%;

  margin-bottom: 3%;
}

select:hover{
  border-bottom: 2px solid #da3743;
}

</style>


