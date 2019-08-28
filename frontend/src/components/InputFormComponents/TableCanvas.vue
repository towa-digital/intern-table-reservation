<template>
  <div>
    <div class="overlay">
      <div class="popup">
        <div class="header">
          <h4 v-html="errormessage"></h4>

          <div class="buttons">
            <button
              type="button"
              :disabled="selected.length == 0"
              @click="onGetReservation"
            >Übernehmen</button>

            <button type="button" @click="onBack">Abbrechen</button>
          </div>

          <div class="legend">
            <span>
              <span class="available"></span> = verfügbar
            </span>
            <span>
              <span class="not-available"></span> = nicht verfügbar
            </span>
            <span>
              <span class="picked"></span> = ausgewählt
            </span>
          </div>
        </div>
        <div class="content" ref="content" @click="useTable" v-resize="redrawCanvas" @mousemove="onMouseMoved">
          <canvas ref="chooseTable" />
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { mapGetters } from 'vuex';
import resize from 'vue-resize-directive';

export default {
  name: 'TableCanvas',
  directives: {
    resize,
  },
  data() {
    return {
      selected: [],
      offset: {
        "x": -1,
        "y": -1
      }
    };
  },
  methods: {
    onMouseMoved(event) {
      this.offset.x = event.offsetX;
      this.offset.y = event.offsetY;
      document.body.style.cursor = "default";

      this.redrawCanvas();
    },
    isTableFree(table) {
      var indexOf = -1;
      for (var i in this.freeTables) {
        if (this.freeTables[i]['id'] == table.id) {
          indexOf = i;
        }
      }

      return indexOf != -1;
    },
    useTable(evt) {
      var canvas = this.$refs.chooseTable;

      var x = evt.offsetX;
      var y = evt.offsetY;

      var that = this;

      this.allTables.forEach(function(table) {
        if (!table.isFree) return;

        var posX = table.position.posX * canvas.width;
        var posY = table.position.posY * canvas.height;
        var width = table.position.width * canvas.width;
        var height = table.position.height * canvas.height;

        if (x >= posX && x <= posX + width && y >= posY && y <= posY + height) {

          if(that.isTableFree(table)) {
            that.selected.push(table);
            that.$store.commit("claimTable", table);
          } else {
            var indexOf = -1;
            for(var i in that.selected) {
              if(that.selected[i].id == table.id) indexOf = i;
            }
            that.selected.splice(indexOf, 1);

            that.$store.commit("freeTable", table);
          }
        }
      });


      this.redrawCanvas();
    },
    redrawCanvas() {
      var canvas = this.$refs.chooseTable;
      if (canvas === undefined) return;

      this.$refs.chooseTable.width = canvas.parentElement.offsetWidth;
      this.$refs.chooseTable.height = canvas.parentElement.offsetHeight;

      var ctx = this.$refs.chooseTable.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      for (var i of this.allTables) {
        var posX = i.position['posX'] * canvas.width;
        var posY = i.position['posY'] * canvas.height;
        var width = i.position['width'] * canvas.width;
        var height = i.position['height'] * canvas.height;

        var strokeX;
        var strokeY;
        var strokeWidth;
        var strokeHeight;

        if (i.position['width'] < 0.15 && i.position['height'] < 0.15) {
          strokeX = i.position['posX'] * canvas.width + i.position['height'] * 0.15 * canvas.height;
          strokeY = i.position['posY'] * canvas.height + i.position['height'] * 0.15 * canvas.height;
          strokeWidth = i.position['width'] * canvas.width - i.position['height'] * 0.3 * canvas.height;
          strokeHeight = i.position['height'] * canvas.height - i.position['height'] * 0.3 * canvas.height;
        } else {
          strokeX = i.position['posX'] * canvas.width + 0.005 * canvas.width;
          strokeY = i.position['posY'] * canvas.height + 0.005 * canvas.width;
          strokeWidth = i.position['width'] * canvas.width - 0.01 * canvas.width;
          strokeHeight = i.position['height'] * canvas.height - 0.01 * canvas.width;
        }

        ctx.fillStyle = '#f5f7f5';
        ctx.fillRect(posX, posY, width, height);
        ctx.lineWidth = 0.5;
        ctx.strokeStyle = '#606361';
        ctx.strokeRect(posX, posY, width, height);

        var isHovered = this.offset.x >= posX && this.offset.x <= posX + width && this.offset.y >= posY && this.offset.y <= posY + height;
       
        if (!i.isFree) {
          ctx.strokeStyle = '#FF0000'; // green
          ctx.fillStyle = '#FF0000';
          ctx.lineWidth = 2;
          ctx.strokeRect(strokeX, strokeY, strokeWidth, strokeHeight);
        } else if (!this.isTableFree(i) || i == this.selected || isHovered){
          ctx.strokeStyle = '#0a35f5'; //blue
          ctx.fillStyle = '#0a35f5';
          ctx.lineWidth = 2;
          ctx.strokeRect(strokeX, strokeY, strokeWidth, strokeHeight);
          if(isHovered){
            document.body.style.cursor = "pointer";
          }
        } else {
          ctx.strokeStyle = '#1d8708'; //red
          ctx.fillStyle = '#1d8708';
          ctx.lineWidth = 2;
          ctx.strokeRect(strokeX, strokeY, strokeWidth, strokeHeight);
        }

        

        ctx.font = '25px sans-serif';

        var fontSize = 25;
        while (ctx.measureText(i.seats + " Plätze").width > strokeWidth && fontSize >= 10) {

          fontSize -= 5;
          ctx.font = fontSize + 'px sans-serif';
        }

        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        if(i.seats == 1){
          ctx.fillText(i.seats + " Platz", posX + width / 2, posY + height / 2);
        } else {
          ctx.fillText(i.seats + " Plätze", posX + width / 2, posY + height / 2);
        }
      }
    },
    onBack() {
      this.$store.commit('setError', '');
      this.$store.commit('decrementStepCounter');
    },
    onGetReservation() {
      var availableSeats = 0;
      var tooMuchTablesForPersons_error = false;
      var tooMuchTablesForPersons_flag = false;

      for (var n of this.selected) {
        if (tooMuchTablesForPersons_flag) tooMuchTablesForPersons_error = true;

        var nosOnTable = n.seats == '' ? 0 : parseInt(n.seats);
        availableSeats += nosOnTable;

        if (availableSeats >= this.numberOfSeats) tooMuchTablesForPersons_flag = true;
      }

      if (availableSeats < this.numberOfSeats) {
        this.$store.commit('setError', 'Zu wenig Tische für alle Gäste ausgewählt!');
      } else if (tooMuchTablesForPersons_error) {
        this.$store.commit('setError', 'Du hast zu viele Tische ausgewählt!');
      } else {
            this.$store.commit('incrementStepCounter');
            this.$store.commit('setError', '');
          }
        }
  },
  computed: {
    ...mapGetters(['freeTables', 'allTables', "errormessage"]),
    tableName() {
      return "";
    },
    numberOfSeats() {
          return this.$store.getters.StepOne.numberOfSeats;
    },
  },
  watch: {
    zoomFactor: function() {
      this.redrawCanvas();
    },
    offsetX: function() {
      this.redrawCanvas();
    },
    offsetY: function() {
      this.redrawCanvas();
    },
    contentWidth: function() {
      this.redrawCanvas();
    },
    contentHeight: function() {
      this.redrawCanvas();
    },
  },
};
</script>

<style scoped>
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  min-width: 800px;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}

.overlay .popup {
  /* margin: 10px; */
  background-color: white;
  /* border-radius: 10px; */
  width: calc(100%);
  height: calc(100%);
}

.header {
  height: 50px;
  border-bottom: 2px solid lightgrey;
}

.content {
  height: calc(100% - 50px);
}

h4 {
  clear: left;
  float: left;

  padding-top: 8px;
  padding-left: 10px;

  margin-block-start: 0rem;
  margin-block-end: 0rem;
}

.buttons {
  clear: right;
  float: right;
}

button {
  margin-top: 10px;
  padding: 8px;
  border: none;
  background: #da3743;
  color: #fff;
  cursor: pointer;
  margin-bottom: 0 !important;

  margin-left: 10px;
  margin-right: 10px;
}

button:hover {
  background: #e15b64;
  border: none;
  margin-bottom: 0 !important;
}

.legend {
  clear: left;
  float: left;

  margin-left: 14px;
  margin-top: 2px;
}

.available {
  display: inline-block;
  width: 10px;
  height: 10px;
  background: #1d8708;
}

.not-available {
  margin-left: 15px;
  display: inline-block;
  width: 10px;
  height: 10px;
  background: #ff0000;
}

.picked {
  margin-left: 15px;
  display: inline-block;
  width: 10px;
  height: 10px;
  background: #0a35f5;
}
</style>