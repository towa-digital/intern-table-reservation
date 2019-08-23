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
  StepOne: {},
  StepTwo: {},
  StepThree: {}
}

const getters = {
  step: state => state.step,
  errormessage: state => state.error.errormessage,
  waitingForAjaxResponse: state => state.waitingForAjaxResponse,
  allTables: state => state.allTables,
  freeTables: state => state.freeTables,
  timeSlots: state => state.timeSlots,
  holidays: state => state.holidays,
  StepOne: state => state.StepOne
}

const actions = {

  //Post new Reservation to API

  addReservation: ({ commit }) => {
    state.waitingForAjaxResponse = true;
    axios.post(
      "http://localhost/wordpress/wp-json/tischverwaltung/v1/savenewreservation",
      {
        from: state.StepOne.from,
        tables: JSON.stringify(state.StepTwo.tables),
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
  },
  claimTable: (state, tableObj) => {
    var index = -1;
    for (var i in state.freeTables) {
      var elem = state.freeTables[i];
      if (elem.id == tableObj.id) index = i;
    }

    if (index != -1) state.freeTables.splice(index, 1);
  },
  freeTable: (state, tableObj) => {
    state.freeTables.push(tableObj);
  },
  setError: (state, errorMsg) => {
    state.error.errormessage = errorMsg;
  },
  setStepOne: (state, data) => {
    state.StepOne = data;
  },
  setStepTwo: (state, data) => {
    state.StepTwo = data;
  },
  setStepThree: (state, data) => {
    state.StepThree = data;
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}