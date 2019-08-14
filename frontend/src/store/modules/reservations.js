import axios from 'axios'

const state = {
  error: {
    errormessage: '',
    error: false
  },
  submitted: {
    submitstatus: false
  }
}

const getters = {
  errormessage: state => state.error.errormessage,
  errorstatus: state => state.error.error,
  submitstatus: state => state.submitted.submitstatus
}

const actions = {
  addReservation: ({ commit }, reservation) => {
    axios
      .post(
        "http://localhost/wordpress/wp-json/tischverwaltung/v1/savenewreservation",
        {
          from: reservation.reservation.from,
          tables: reservation.reservation.tables,
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
  }
}

const mutations = {
  reservationDenied: (state, data) => {
    state.error.errormessage = data;
    state.error.error = true;
  },
  reservationAccepted: (state) => {
    state.submitted.submitstatus = true;
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}