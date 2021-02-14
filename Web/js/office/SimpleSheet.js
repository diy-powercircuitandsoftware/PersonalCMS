class SimpleSheet {
    constructor(...args) {
        if (typeof args[0] === 'string' || args[0] instanceof String) {
            this.Sheet = document.querySelector(args[0]).appendChild(document.createElement("TABLE"));
        } else if (args[0] instanceof HTMLElement) {
            this.Sheet = args[0].appendChild(document.createElement("TABLE"));
        } else {
            this.Sheet = document.body.appendChild(document.createElement("TABLE"));
        }
        this.Sheet.border = "1";
        this.Sheet.style.textAlign = "center";
        this.Sheet.style.tableLayout = "fixed";
        this.Sheet.style.width = "100%";
    }
    SetRowCol(r, c) {
        var rowlen = this.Sheet.rows.length - 1;
        if (rowlen < r) {
            for (var i = rowlen; i < r; i++) {
                this.Sheet.insertRow(-1).insertCell(-1).innerHTML = "Row_" + (i + 1);
            }
        } else if (rowlen > r) {
            for (var i = rowlen; i > r; i--) {
                this.Sheet.deleteRow(i);
            }
        }

        for (var row = 0; row < this.Sheet.rows.length; row++) {
            var collen = this.Sheet.rows[row].cells.length - 1;
            if (collen < c) {
                for (var i = collen; i < c; i++) {
                    var newcell = this.Sheet.rows[row].insertCell(-1);
                    if (row > 0) {
                        newcell.innerHTML = '<input style="width: 100%;box-sizing: border-box;" type="text" name="" value="" />';
                    } else {
                        newcell.innerHTML = "Cell_" + (i + 1);
                    }

                }
            }
            if (collen > c) {
                for (var i = collen; i > c; i--) {
                    this.Sheet.rows[row].deleteCell(i);
                }
            }
        }

        this.Sheet.rows[0].cells[0].innerHTML = "";


    }

}

function SpreadSheet(c, r) {
    var Method = document.createElement("TABLE");
    Method.border = "1";
    Method.style.textAlign = "center";



    Method.addEventListener("keyup", function (e) {
        if (e.keyCode == 13) {
            var maxrow = this.rows.length;

            var rowindex = (e.target.parentNode.parentNode.rowIndex);
            var cellindex = (e.target.parentNode.cellIndex);

            if (rowindex + 1 < maxrow) {
                var maxcell = this.rows[rowindex + 1].cells.length;
                if (cellindex < maxcell) {
                    this.rows[rowindex + 1].cells[cellindex].querySelector('input[type=text]').focus();
                }
            } else if (rowindex >= 1) {
                rowindex = 1;
                var maxcell = this.rows[rowindex].cells.length;
                if (cellindex + 1 < maxcell) {
                    this.rows[rowindex ].cells[cellindex + 1].querySelector('input[type=text]').focus();
                }
            }
        }
    });
    Method.AddPasteCSV = function () {
        Method.addEventListener("paste", function (e) {
            var clipboardData, pastedData;
            e.stopPropagation();
            e.preventDefault();
            clipboardData = e.clipboardData || window.clipboardData;
            pastedData = clipboardData.getData('Text');
            var spcomma = pastedData.split(",");
            if (spcomma.length > 1) {
                var cellindex = (e.target.parentNode.cellIndex);
                var rowindex = (e.target.parentNode.parentNode.rowIndex);
                for (var i = 0; i < spcomma.length; i++) {
                    this.rows[rowindex].cells[cellindex + i].querySelector("input").value = spcomma[i];
                }
            } else {
                e.target.value = pastedData;
            }
            return false;
        });
    }
    Method.ClearAll = function () {
        [].forEach.call(this.querySelectorAll('input[type=text]'), function (inp) {
            inp.value = "";
        });
    };
    Method.ExportToCSVArray = function () {
        var rowdata = this.rows;
        var dataout = [];
        for (var i = 0; i < rowdata.length; i++) {
            var tempdata = [];
            [].forEach.call(rowdata[i].querySelectorAll('input[type=text]'), function (inp) {
                tempdata.push(inp.value);
            });
            dataout[i] = tempdata;
        }
        return dataout;
    };
    Method.GetAllNumber = function () {
        var Out = [];

        [].forEach.call(this.querySelectorAll('input[type=text]'), function (inp) {
            var reg = /^-?\d+\.?\d*$/;
            if (reg.test(inp.value)) {
                Out.push(parseFloat(inp.value));
            }

        });
        return Out;
    };
    Method.GetNumberAtCell = function (cellindex) {
        var Out = [];
        var r = this.rows;
        for (var ir = 0; ir < r.length; ir++) {
            var inp = r[ir].cells[parseInt(cellindex)].querySelector('input[type=text]');
            var reg = /^-?\d+\.?\d*$/;
            if (inp !== null && reg.test(inp.value)) {
                Out.push(parseFloat(inp.value));
            }
        }
        return Out;
    };

    Method.ImportFromCSV = function (array) {
        this.innerHTML = "";
        var startrow = Method.insertRow(-1);
        startrow.insertCell(-1).innerHTML = "";
        for (var cc = 1; cc <= array[0].length; cc++) {
            startrow.insertCell(-1).innerHTML = "cell" + cc;
        }

        for (var rr = 0; rr < array.length; rr++) {
            var nextrow = Method.insertRow(-1);
            nextrow.insertCell(-1).innerHTML = "row" + (parseInt(rr) + 1);
            var subdata = array[rr];

            for (var cc = 0; cc < subdata.length; cc++) {
                nextrow.insertCell(-1).innerHTML = '<input type="text" name="" value="' + subdata[cc] + '" />';
            }
        }
    };

    Method.Reset();
    /* Method.AddRow=function(n){
     this.insertRow(-1).insertCell(-1).appendChild(document.createElement("INPUT"));
     };*/



    return Method;
}