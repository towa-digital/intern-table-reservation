<template>
  <div>
    <div class="overlay">
      <div class="popup">
        <div class="header">
         <!-- <h4 v-html="errormessage"></h4>-->

          <div class="buttons">
            <button
              type="button"
              :disabled="selectedTables.length == 0"
              @click="onGetReservation"
              v-resize="redrawCanvas"
            >Weiter</button>

            <button type="button" @click="onBack">Zurück</button>
          </div>

          <div class="legend">
            <p class="canvasError" v-html="errormessage"></p>
            <div class="quadratParent">
              <span>
                <span class="quadrat" style="background: #1d8708"></span> = verfügbar
              </span>
              <span>
                <span class="quadrat" style="background: #ff0000"></span> = nicht verfügbar
              </span>
              <span>
                <span class="quadrat" style="background: #0a35f5"></span> = ausgewählt
              </span>
            </div>
          </div>
        </div>

        <div
          class="content"
          ref="content"
          @click="useTable"
          v-resize="redrawCanvas"
          @mousemove="onMouseMoved"
        >
          <div class="backgroundImgIn" v-if="!isOutside" v-on:resize="redrawCanvas">
            <canvas ref="chooseTable" />
          </div>
          <div class="backgroundImgOut" v-if="isOutside" v-on:resize="redrawCanvas">
            <canvas ref="chooseTable" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { mapGetters } from 'vuex';
import resize from 'vue-resize-directive';
// window.addEventListener("resize", this.redrawCanvas())

export default {
  name: 'TableCanvas',
  directives: {
    resize,
  },
  data() {
    return {
      errormessage: "",
      offset: {
        x: -1,
        y: -1,
      },
    };
  },
  methods: {
    onMouseMoved(event) {
      this.offset.x = event.offsetX;
      this.offset.y = event.offsetY;
      document.body.style.cursor = 'default';

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
          if (that.isTableFree(table)) {
            that.$store.commit('claimTable', table);
          } else {
            that.$store.commit('freeTable', table);
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

        const innerDistance = 5;
        var strokeX = posX + innerDistance;
        var strokeY = posY + innerDistance;
        var strokeWidth = width - (2 * innerDistance);
        var strokeHeight = height - (2 * innerDistance);

        ctx.setLineDash([]);

        ctx.fillStyle = '#f5f7f5';
        ctx.fillRect(posX, posY, width, height);
        ctx.lineWidth = 0.5;
        ctx.strokeStyle = '#606361';
        ctx.strokeRect(posX, posY, width, height);

        var isHovered =
          this.offset.x >= posX &&
          this.offset.x <= posX + width &&
          this.offset.y >= posY &&
          this.offset.y <= posY + height;


        if (!i.isFree) {
          ctx.strokeStyle = '#FF0000'; // red
          ctx.fillStyle = '#FF0000';
          ctx.lineWidth = 2;
          ctx.strokeRect(strokeX, strokeY, strokeWidth, strokeHeight);
        } else if (!this.isTableFree(i) || isHovered) {
          if(isHovered && this.isTableFree(i)) ctx.setLineDash([2, 2]);
          ctx.strokeStyle = '#0a35f5'; //blue
          ctx.fillStyle = '#0a35f5';
          ctx.lineWidth = 2;
          ctx.strokeRect(strokeX, strokeY, strokeWidth, strokeHeight);
          if (isHovered) {
            document.body.style.cursor = 'pointer';
          }
        } else {
          ctx.strokeStyle = '#1d8708'; //green
          ctx.fillStyle = '#1d8708';
          ctx.lineWidth = 2;
          ctx.strokeRect(strokeX, strokeY, strokeWidth, strokeHeight);
        }

        ctx.font = '25px sans-serif';

        var fontSize = 25;

        while (ctx.measureText(i.seats + ' Plätze').width > strokeWidth && fontSize >= 10) {
          fontSize -= 5;
          ctx.font = fontSize + 'px sans-serif';
        }

        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        if(fontSize < 11){
          ctx.font = '25px sans-serif'
           ctx.fillText(i.seats, posX + width / 2, posY + height / 2);
        } else {
          if (i.seats == 1) {
          ctx.fillText(i.seats + ' Platz', posX + width / 2, posY + height / 2);
        } else {
          ctx.fillText(i.seats + ' Plätze', posX + width / 2, posY + height / 2);
        }
        }
      }
    },
    onBack() {
      this.$store.commit('decrementStepCounter');
    },
    onGetReservation() {
      var availableSeats = 0;
      var tooMuchTablesForPersons_error = false;
      var tooMuchTablesForPersons_flag = false;

      /*
       * Array der ausgewählten Tische sortieren, damit im Anschluss daran korrekt geprüft werden kann,
       * ob zu viele Tische ausgewählt werden können.
       */
      this.selectedTables.sort(function(a, b) {
        return b.seats - a.seats;
      });

      for (var n of this.selectedTables) {
        if (tooMuchTablesForPersons_flag) tooMuchTablesForPersons_error = true;

        var nosOnTable = n.seats == '' ? 0 : parseInt(n.seats);
        availableSeats += nosOnTable;

        if (availableSeats >= this.numberOfSeats) tooMuchTablesForPersons_flag = true;
      }


      if (availableSeats < this.numberOfSeats) {
        this.errormessage = 'Zu wenig Tische für alle Gäste ausgewählt!';
      } else if (tooMuchTablesForPersons_error) {
        this.errormessage = 'Du hast zu viele Tische ausgewählt!';
      } else {
        this.$store.commit('incrementStepCounter');
        this.errormessage = "";
      }
    },
  },
  mounted() {
    window.addEventListener("resize", this.redrawCanvas)
  },
  computed: {
    ...mapGetters(['freeTables', 'allTables', 'selectedTables']),
    tableName() {
      return '';
    },
    numberOfSeats() {
      return this.$store.getters.StepOne.numberOfSeats;
    },
    isOutside() {
      return this.$store.getters.StepOne.location == 1;
    }
  },
  watch: {
    contentWidth: function() {
      this.redrawCanvas();
    },
    contentHeight: function() {
      this.redrawCanvas();
    },
    freeTables: function() {
      this.redrawCanvas();
    }
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
  width: 100%;
  height: 100%;
}

.header {
  height: 52px;
  border-bottom: 2px solid lightgrey;
}

.content {
  height: calc(100% - 62px);
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
  width: 100px;
  margin-top: 10px;
  padding: 8px;
  border: none;
  background: #da3743;
  color: #fff;
  cursor: pointer;
  margin-bottom: 0 !important;

  margin-left: 10px;
  margin-right: 10px;

   -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
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

  height: 100%;
}

.quadratParent {
  font-size: 12.5px;
  position: relative;
  left: 0px;
  bottom: 0px;
   -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
}

.quadrat {
  display: inline-block;
  width: 10px;
  height: 10px;
}


.backgroundImgIn {
  height: 100%;
  width: 100%;

  background-image: url(./../../../assets/maxresdefault.jpg);
  background-size: 100% 100%;
  background-repeat: no-repeat;
  background-clip: padding-box;
  background-position: center;
}

.backgroundImgOut {
  height: 100%;
  width: 100%;

  background-image: url(./../../../assets/outside.jpg);
  background-size: 100% 100%;
  background-repeat: no-repeat;
  background-clip: padding-box;
  background-position: center;
}

.canvasError {
  margin: 0;
  font-size: 20px;
  color: darkred;
  white-space: nowrap;
  line-height: 35px;
  font-weight: bolder;
}

.canvasError:after {
  content:"";
  display:inline-block;
  width:0px;
  
}
</style>