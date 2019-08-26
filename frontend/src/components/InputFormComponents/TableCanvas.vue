<template>
    <div>
        <div class="overlay">
            <div class="popup">
                <div class="header">
                    Tisch:: {{tableName}}
                    <button type="button" :disabled="selected === undefined" @click="emitToParent">Übernehmen</button>
                    <button type="button" @click="cancelAndEmitToParent">Abbrechen</button>
                </div>
                <div class="content" ref="content" @click="useTable" v-resize="redrawCanvas">
                    <canvas ref="chooseTable"/>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import { mapGetters } from 'vuex';
import resize from 'vue-resize-directive'


export default {
    name: "TableCanvas",
    directives: {
        resize,
    },
    data() {
        return {
            selected: undefined,
        }
    },
    methods: {
        isTableFree(table) {
            var indexOf = -1;
            for(var i in this.freeTables) {
                if(this.freeTables[i]["id"] == table.id) {
                  indexOf = i;
                }
            }

            return indexOf != -1;
        },
        emitToParent() {
            this.$emit("input", {
                "timestamp": new Date(),
                "value": this.selected
            });
        },
        cancelAndEmitToParent() {
            this.$emit("input", {
                "timestamp": new Date(),
                "value": undefined
            });
        },
        useTable(evt) {
            var canvas = this.$refs.chooseTable;

            var x = evt.offsetX;
            var y = evt.offsetY;
            
            var that = this;
            var isSet = false;

            this.allTables.forEach(function(table) {
                if(! that.isTableFree(table) || ! table.isFree) return;



                var posX = table.position.posX * canvas.width;
                var posY = table.position.posY * canvas.height;
                var width = table.position.width * canvas.width;
                var height = table.position.height * canvas.height;

                if(x >= posX && x <= posX + width && y >= posY && y <= posY + height) {
                    isSet = true;
                    that.selected = table;
                }
            });

            if(! isSet) this.selected = undefined;

        },
        redrawCanvas() {
            var canvas = this.$refs.chooseTable;
            if(canvas === undefined) return;

            this.$refs.chooseTable.width = canvas.parentElement.offsetWidth;
            this.$refs.chooseTable.height = canvas.parentElement.offsetHeight;

            var ctx = this.$refs.chooseTable.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for(var i of this.allTables) {
                var posX = (i.position["posX"]) * canvas.width;
                var posY = (i.position["posY"]) * canvas.height;
                var width = i.position["width"] * canvas.width;
                var height = i.position["height"] * canvas.height;

                ctx.fillStyle = (this.isTableFree(i) && i.isFree) ? "#000000" : "#ff0000";
                ctx.font = "30px serif";

                var fontSize = 30;
                while(ctx.measureText(i.seats) > width) {
                    fontSize -= 5;
                    ctx.font = fontSize + "px serif";
                }

                ctx.textAlign = "center";
                ctx.textBaseline = "middle";

                ctx.fillRect(posX, posY, width, height);
                ctx.fillStyle = "#ffffff";
                console.log(posX + width / 2);
                console.log(posY + height / 2);
                ctx.fillText(i.seats, posX + (width / 2), posY + (height / 2));
            }

            debugger;

        }
    },
    computed: {
        ...mapGetters(["freeTables", "allTables"]),
        tableName() {
            if(this.selected === undefined) return "Kein Tisch ausgewählt";
            else return this.selected.title;
        }
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
        }
    }
}
</script>

<style scoped>
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 90%;
        height: 100%;
        background-color:rgba(0, 0, 0, 0.5);
    }

    .overlay .popup {
        margin: 10px;
        background-color: white;
        border-radius: 10px;
        width: calc(100% - 20px);
        height: calc(100% - 20px);
    }

    .header {
        height: 30px;
    }

    .content {
        height: calc(100% - 30px);
    }

</style>