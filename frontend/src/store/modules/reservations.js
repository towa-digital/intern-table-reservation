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
<<<<<<< HEAD
    const response = axios
=======
    axios
>>>>>>> db1e8d5f248167895f6293b61b9672f31920a6d3
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
<<<<<<< HEAD
      .then((response) => {
        // this.submitted = true;
=======
      .then(() => {
>>>>>>> db1e8d5f248167895f6293b61b9672f31920a6d3
        commit("reservationAccepted")

      })
      .catch(error => {
<<<<<<< HEAD
        //   if (error.response) this.errormessage = error.response.data.message;
        //   this.error = true;

        //   console.log(error)
        commit('reservationDenied', error.response.data.message)

        console.log(error.response)
      })




=======
        commit('reservationDenied', error.response.data.message)
      })
>>>>>>> db1e8d5f248167895f6293b61b9672f31920a6d3
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