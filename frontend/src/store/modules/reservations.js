import axios from 'axios'

const state = {
  error: {
    errormessage: '',
    error: false
  },
  submitted: {
    submitstatus: false
  },
  tables: []
}

const getters = {
  errormessage: state => state.error.errormessage,
  errorstatus: state => state.error.error,
  submitstatus: state => state.submitted.submitstatus,
  allTables: state => state.tables
}

const actions = {

  //Post new Reservation to API

  addReservation: ({ commit }, reservation) => {
    axios
      .post(
        "http://localhost/wordpress/wp-json/tischverwaltung/v1/savenewreservation",
        {
          from: reservation.reservation.from,
          tables: JSON.stringify([reservation.reservation.tables]),
          firstname: reservation.reservation.firstname,
          lastname: reservation.reservation.lastname,
          phonenumber: reservation.reservation.phonenumber,
          mail: reservation.reservation.mail,
          numberOfSeats: reservation.reservation.numberOfSeats
        }
      )
      .then(() => {
        commit("reservationAccepted")

      })
      .catch(error => {
        commit('reservationDenied', error.response.data.message)
      })
  },

  // Get all Tables from API

  fetchTables: ({ commit }, reservation) => {
    axios.get('http://localhost/wordpress/wp-json/tischverwaltung/v1/freetables/' + reservation.reservation.from + '/' + reservation.reservation.numberOfSeats
    )
      .then((response) => {
        commit('setTables', response.data)
        reservation.reservation.stepOne = false
        reservation.reservation.stepTwo = true
      })
      .catch(error => {
        commit('reservationDenied', error.response.data.message)
        reservation.reservation.stepOne = true;
        reservation.reservation.stepTwo = false;
      })
  }
}


const mutations = {
  reservationDenied: (state, data) => {
    state.error.errormessage = data;
    state.error.error = true;
  },
  reservationAccepted: (state) => {
    state.submitted.submitstatus = true;
    state.error.error = false;
  },
  setTables: (state, tables) => (state.tables = tables)
}

export default {
  state,
  getters,
  actions,
  mutations
}