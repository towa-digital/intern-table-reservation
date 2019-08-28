import axios from 'axios'

const state = {
  error: {
    errormessage: '',
  },
  step: 1,
  waitingForAjaxResponse: false,
  freeTables: [],
  allTables: [],
  timeSlots: [],
  holidays: [],
  options: [],
  StepOne: {
    "date": undefined,
    "from": 0,
    "location": 0,
    "numberOfSeats": 0
  },
  StepTwo: {
    "selectedTables": []
  },
  StepThree: {
    "firstname": "",
    "lastname": "",
    "mail": "",
    "phonenumber": "",
    "remarks": ""
  }
}

const getters = {
  step: state => state.step,
  errormessage: state => state.error.errormessage,
  waitingForAjaxResponse: state => state.waitingForAjaxResponse,
  allTables: state => state.allTables,
  freeTables: state => state.freeTables,
  selectedTables: state => state.StepTwo.selectedTables,
  timeSlots: state => state.timeSlots,
  holidays: state => state.holidays,
  StepOne: state => state.StepOne,
  options: state => state.options,
}

const actions = {

  loadOptions: () => {
    axios.get("http://localhost/wordpress/wp-json/tischverwaltung/v1/getoptions", {})
      .then(response => {
        state.options = response.data;
      })
      .catch(response => {
        alert("Ein Fehler ist aufgetreten! Versuch es bitte später erneut!");
      });
  },

  //Post new Reservation to API

  addReservation: ({commit}) => {
    state.waitingForAjaxResponse = true;

    var selectedTableIds = [];
    for(var tableObj of state.StepTwo.selectedTables) {
      selectedTableIds.push(tableObj.id);
    }

    axios.post(
      "http://localhost/wordpress/wp-json/tischverwaltung/v1/savenewreservation",
      {
        from: state.StepOne.from,
        tables: JSON.stringify(selectedTableIds),
        firstname: state.StepThree.firstname,
        lastname: state.StepThree.lastname,
        phonenumber: state.StepThree.phonenumber,
        mail: state.StepThree.mail,
        numberOfSeats: state.StepOne.numberOfSeats,
        remarks: state.StepThree.remarks
      }
    )
      .then(() => {
        state.waitingForAjaxResponse = false;
        commit("incrementStepCounter");

      })
      .catch(error => {
        state.waitingForAjaxResponse = false;
        commit('reservationDenied', error.response.data.message)
      })
  },

  // Get all Tables from API

  fetchTables: ({ commit }, time) => {
    state.waitingForAjaxResponse = true;
    axios.get('http://localhost/wordpress/wp-json/tischverwaltung/v1/freetables/' + time.time.from + '/' + time.time.numberOfSeats + '/' + state.StepOne.location
    )
      .then((response) => {
        state.waitingForAjaxResponse = false;

        commit('setTables', response.data)
        commit("incrementStepCounter");

      })
      .catch(error => {
        state.waitingForAjaxResponse = false;

        commit('reservationDenied', error.response.data.message)
      })
  },
  getTimeSlots: ({ commit }) => {
    state.waitingForAjaxResponse = true;
    axios.get('http://localhost/wordpress/wp-json/tischverwaltung/v1/gettimeslots')
      .then((response) => {
        state.waitingForAjaxResponse = false;
        commit("onTimeSlotLoad", response.data);
      }).catch(error => {
        state.waitingForAjaxResponse = false;
        commit('reservationDenied', error.response.data.message)
      });
  },

}


const mutations = {
  incrementStepCounter: (state) => {
    state.step++;
  },
  decrementStepCounter: (state) => {
    state.step--;
  },
  onTimeSlotLoad: (state, data) => {
    state.timeSlots = data.openingHours;

    state.holidays = [];
    for (var h of data.holidays) {
      state.holidays.push(new Date(h * 1000));
    }
  },
  reservationDenied: (state, data) => {
    state.error.errormessage = data;
  },
  setTables: (state, tables) => {
    state.allTables = JSON.parse(JSON.stringify(tables));
    state.freeTables = JSON.parse(JSON.stringify(tables));

    for(var singleSelectedTable of state.StepTwo.selectedTables) {
      var indexOf = -1;

      for(var j in state.freeTables) {
        if(state.freeTables[j].id == singleSelectedTable.id) {
          indexOf = j;
        }
      }

      if(indexOf != -1) state.freeTables.splice(indexOf, 1);
    }
  },
  claimTable: (state, tableObj) => {
    // von freeTables entfernen
    var index = -1;
    for (var i in state.freeTables) {
      var elem = state.freeTables[i];
      if (elem.id == tableObj.id) index = i;
    }

    if (index != -1) state.freeTables.splice(index, 1);

    // zu selectedTables hinzufügen
    state.StepTwo.selectedTables.push(tableObj);
  },
  freeTable: (state, tableObj) => {
    // zu freeTables hinzufügen
    state.freeTables.push(tableObj);

    // von selectedTables entfernen
    var index = -1;
    for(var i in state.StepTwo.selectedTables) {
      var elem = state.StepTwo.selectedTables[i];
      if(elem.id == tableObj.id) index = i;
    }

    if(index != -1) state.StepTwo.selectedTables.splice(index, 1);
  },
  setError: (state, errorMsg) => {
    state.error.errormessage = errorMsg;
  },
  setStepOne: (state, payload) => {
    state.StepOne = payload;
  },
  // setStepTwo: (state, payload) => {
  //  // state.StepTwo = payload;
  // },
  setStepThree: (state, payload) => {
    state.StepThree = payload;
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}