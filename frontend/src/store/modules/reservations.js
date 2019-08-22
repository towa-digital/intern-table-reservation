import axios from 'axios'

const state = {
  error: {
    errormessage: '',
  },
  submitted: {
    submitstatus: false
  },
  waitingForAjaxResponse: false,
  freeTables: [],
  allTables: [],
  timeSlots: [],
  holidays: []
}

const getters = {
  errormessage: state => state.error.errormessage,
  submitstatus: state => state.submitted.submitstatus,
  waitingForAjaxResponse: state => state.waitingForAjaxResponse,
  allTables: state => state.allTables,
  freeTables: state => state.freeTables,
  timeSlots: state => state.timeSlots,
  holidays: state => state.holidays,
}

const actions = {

  //Post new Reservation to API

  addReservation: ({ commit }, reservation) => {
    state.waitingForAjaxResponse = true;
    axios.post(
        "http://localhost/wordpress/wp-json/tischverwaltung/v1/savenewreservation",
        {
          from: reservation.reservation.from,
          tables: JSON.stringify(reservation.reservation.tables),
          firstname: reservation.reservation.firstname,
          lastname: reservation.reservation.lastname,
          phonenumber: reservation.reservation.phonenumber,
          mail: reservation.reservation.mail,
          numberOfSeats: reservation.reservation.numberOfSeats
        }
      )
      .then(() => {
        state.waitingForAjaxResponse = false;
        commit("reservationAccepted")

      })
      .catch(error => {
        state.waitingForAjaxResponse = false;
        commit('reservationDenied', error.response.data.message)
      })
  },

  // Get all Tables from API

  fetchTables: ({ commit }, reservation) => {
    state.waitingForAjaxResponse = true;
    axios.get('http://localhost/wordpress/wp-json/tischverwaltung/v1/freetables/' + reservation.reservation.from + '/' + reservation.reservation.numberOfSeats
    )
      .then((response) => {
        state.waitingForAjaxResponse = false;
        commit('setTables', response.data)
        reservation.reservation.step++;
      })
      .catch(error => {
        state.waitingForAjaxResponse = false;
        commit('reservationDenied', error.response.data.message)
      })
  },
  getTimeSlots: ({ commit }, reservation) => {
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
  onTimeSlotLoad: (state, data) => {
    state.timeSlots = data.openingHours;

    state.holidays = [];
    for(var h of data.holidays) {
      state.holidays.push(new Date(h * 1000));
    }
  },
  reservationDenied: (state, data) => {
    state.error.errormessage = data;
  },
  reservationAccepted: (state) => {
    state.submitted.submitstatus = true;
  },
  setTables: (state, tables) => {
    state.allTables = JSON.parse(JSON.stringify(tables));
    state.freeTables = JSON.parse(JSON.stringify(tables));
  },
  claimTable: (state, tableObj) => {
    var index = -1;
    for(var i in state.freeTables) {
      var elem = state.freeTables[i];
      if(elem.id == tableObj.id) index = i;
    }

    if(index != -1) state.freeTables.splice(index, 1);
  },
  freeTable: (state, tableObj) => {
    state.freeTables.push(tableObj);
  },
  setError: (state, errorMsg) => {
    state.error.errormessage = errorMsg;
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}