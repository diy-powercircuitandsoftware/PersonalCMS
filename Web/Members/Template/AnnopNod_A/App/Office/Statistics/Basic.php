<?php
session_start();
include_once '../../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../../Auth/Action/VerifySession.php';
$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$event = new Event_Reader(new Event_Database($config));
if ($config->IsOnline() && isset($_SESSION["User"])) {
    $modlist = array();
    foreach ($module->LoadModule(Module_Database::Access_Member) as $value) {
        include_once $module->ModulePath . $value["dirname"] . "/init.php";
        $cn = new $value["classname"]();
        $cn->SetUserID($_SESSION["User"]["id"]);
        $modlist[] = $cn;
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Open Document</title>
            <link rel="stylesheet" type="text/css" href="../../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../../css/PersonalCMS.css">
            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>

            <script src="../../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../../js/office/SimpleSheet.js"></script>
            <script src="../../../../../../js/office/Statistical/Basic.js"></script>
            <script src="../../../../../../js/office/Statistical/BellChart.js"></script>
            <script src="../../../../../../js/office/Statistical/Chart.js"></script>
            <style>
                .BNShowDialog{
                    border-style: outset;
                    border-width: thin;
                    margin-left: 1px;
                    min-height: 22px;
                    min-width: 22px;

                }
                .BNExec{
                    border-style: outset;
                    border-width: thin;
                    margin-left: 1px;
                    min-height: 22px;
                    min-width: 22px;
                }
                .BNExecMultiple{
                    border-style: outset;
                    border-width: thin;
                    margin-left: 1px;
                    min-height: 22px;
                    min-width: 22px;
                }
                .BNExeCellChart{
                    border-style: outset;
                    border-width: thin;
                    margin-left: 1px;
                    min-height: 22px;
                    min-width: 22px;
                }

                .BNBolder{
                    border-style: outset;
                    border-width: thin;
                    margin-left: 1px;
                    min-height: 22px;
                    min-width: 22px;

                }
            </style>

            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var chart = new Chart(document.getElementById("ImageOutput"));
                    var sd = new SuperDialog();
                    var stat = new Statistical_Basic();
                    var ssh = new SimpleSheet("#SpreadSheet");
                    var bc = new BellChart(document.getElementById("ImageOutput"));
                    ssh.SetRowCol(12, 12);
                    ssh.CSS("position", "absolute");
                    //http://ie.eng.cmu.ac.th/IE2014/elearnings/2015_01/183/Minitab.pdf
                    //http://www.stvc.ac.th/elearning/stat/csu5.html
                    /*   if (ss.URLParam()["path"] !== undefined) {
                     var dpw = sd.PleaseWait().ZIndex(999);
                     ss.Post("../../../../Api/Ajax/CSV/GetCSVData.php", {"path": ss.URLParam()["path"]}, function (data) {
                     data = JSON.parse(data);
                     ssh.ImportFromCSV(data["csvdata"]);
                     document.title = data["name"];
                     dpw.Close();
                     });
                     }*/


                    ss.S("#BNClear").Click(function () {
                        if (ss.URLParam()["path"] !== undefined) {
                            sd.Confirm("Do You Clear Data", function () {
                                ssh.ClearAll();
                            }).ZIndex(999);
                        } else {
                            ssh.Reset();
                        }

                    });
                    ss.S("#BNClearOutput").Click(function () {
                        ss.S("#TXTOutput").Val("");
                    });

                    ss.S(".BNExec").Click(function () {
                        ss.S("#TXTOutput").Val(ss.S("#TXTOutput").Val() + "\n" + this.getAttribute("data-cmd") + "=" + stat[this.getAttribute("data-cmd")](ssh.GetAllNumber()));
                    });

                    ss.S(".BNExecMultiple").Click(function () {

                        var cmd = this.getAttribute("data-cmd");
                        var ssaindex = this.getAttribute("data-spreadsheetarrayindex");
                        var t = sd.TableLayout(function () {
                            var argss = [];

                            for (var i = 0; i < t.args.length; i++) {
                                argss.push(t.args[i].value);
                            }
                            if (ssaindex !== null) {
                                var spreadsheetarrayindex = parseInt(ssaindex);
                                argss.splice(spreadsheetarrayindex, 0, ssh.GetAllNumber());
                            }
                            ss.S("#TXTOutput").Val(ss.S("#TXTOutput").Val() + "\n" + cmd + "=" + stat[cmd].apply(stat, argss));
                            return true;
                        }).ZIndex(999).Title(this.getAttribute("data-cmd"));
                        t.args = [];
                        var sp = this.getAttribute("data-input").split(",");
                        for (var i = 0; i < sp.length; i++) {
                            var spclock = sp[i].split(":");
                            if (spclock[1] == "number") {
                                var input = document.createElement("INPUT");
                                input.type = "number";
                                input.style.width = "99%";
                                t.AddNewRowElement(spclock[0], input);
                                t.args.push(input);
                            }
                        }

                    });
                    ss.S(".BNExeCellChart").Click(function () {

                        var cmd = this.getAttribute("data-cmd");
                        var dat = ssh.GetAllNumber();
                        for (var i = 0; i < dat.length; i++) {
                            chart.SetData(i + 1, dat[i]);
                        }
                        chart[cmd]();

                    });
                    ss.S("#BNOpen").Click(function () {

                        var SaveBeforeExit = sd.SaveBeforeExit("Do You Save Before Open Document").ZIndex(999).Title("New Document");
                        SaveBeforeExit.OnDiscard = function () {
                            window.onbeforeunload = null;
                            window.location.replace("MainPage.php");

                        };
                        SaveBeforeExit.OnSave = function () {
                            ssh.AfterSave = function () {
                                window.onbeforeunload = null;
                                window.location.replace("MainPage.php");
                            };
                            ss.S("#BNSave").Click();
                        };
                    });

                    ss.S("#BNResize").Click(function () {

                        sd.RowCol(function (v) {
                            ssh.SetRowCol(v.Row, v.Column);
                            return true;
                        });

                    });

                    ss.S("#BNSave").Click(function () {
                        var dpw = sd.PleaseWait().ZIndex(999);
                        ss.Post("../../../../Api/Ajax/CSV/SaveCSVFile.php", {"path": ss.URLParam()["path"], "csvdata": ssh.ExportToCSVArray()}, function (data) {
                            if (data == "1") {
                                dpw.Close();
                                if (ssh.AfterSave) {
                                    ssh.AfterSave();
                                }
                            } else {

                            }
                        });
                    });
                    ss.S("#OpenDialogBellChart").Click(function () {
                        sd.ImportOkCancel("BellChart", "#BellChartDialog", function () {

                            var sv = ss.S("#BellChartSelect").Val();
                            var za21 = stat.ConfidenceLevelTOZA2(parseFloat(ss.S("#BellChartCL1").Val()) / 100);
                            var za22 = stat.ConfidenceLevelTOZA2(parseFloat(ss.S("#BellChartCL2").Val()) / 100);
                            var arrdata = ssh.GetAllNumber();
                            var m = stat.Average(arrdata);
                            var sd = stat.StandardDeviation(arrdata);
                            var txtout = ss.S("#TXTOutput").Val() + "\n";
                            if (sv == "1")
                            {
                                bc.Above(za21, m, sd);
                                txtout = txtout + "Bell Above=" + (stat.Bell_Above(za21, arrdata));
                            } else if (sv == "2")
                            {
                                bc.Below(za21, m, sd);
                                txtout = txtout + "Bell Below=" + (stat.Bell_Below(za21, arrdata));
                            } else if (sv == "3")
                            {
                                bc.Between(za22, za21, m, sd);
                                txtout = txtout + "Bell Between=" + (stat.Bell_Between(za21, za22, arrdata));
                            } else if (sv == "4")
                            {
                                bc.Outside(za22, za21, m, sd);
                                txtout = txtout + "Bell Outside=" + (stat.Bell_Outside(za21, za22, arrdata));
                            }
                            ss.S("#TXTOutput").Val(txtout);
                            return true;
                        }).ZIndex(999).Title("Bell Curve");
                    });




                });
            </script>
        </head>
        <body class="HolyGrail">
            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a class="MenuLink" style="display: inline;" href="../../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>

            <div class="HolyGrail-body">
                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {

                            printf('  <a class="MenuLink" href="%s">%s</a>', "../../../App/" . $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Nav)) {
                            echo ' <div class="BorderBlock" style="margin-top: 1px;" >';
                            printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                            echo $value->Execute(Module_SDK_Basic::Layout_Nav);
                            echo '</div>';
                        }
                    }
                    ?>     
                </nav>
                <main >
                    <div style="background-color: burlywood;border-style: solid;border-width: thin;">

                        <img id="BNOpen"  style="border-style: outset;" src="../../../../../../img/io/open.gif" width="22" height="22">
                        <?php
                        if (isset($_GET["path"]) && $_SESSION["User"]["writable"] == 1) {
                            echo '<img id="BNSave"  style="border-style: outset;" src="../../../../../../img/io/save.gif" width="22" height="22">';
                        }
                        ?>
                        <img id="BNClear" style="border-style: outset;" src="../../../../../../img/wysiwyg/removeformat.gif" width="22" height="22">
                        <img id="BNResize" style="border-style: outset;" src="../../../../../../img/pointpoint/resize.jpg" width="22" height="22">
                    </div>

                    <div style="background-color: burlywood;border-style: solid;border-width: thin;">
                        <button data-cmd="Average" class="BNExec">x&#772;</button>
                        <button data-cmd="GeometricMean" class="BNExec">G.M.</button>
                        <button data-cmd="HarmonicMean" class="BNExec">H.M.</button>
                        <button data-cmd="Sum" class="BNExec">&Sigma;</button>
                        <button data-cmd="PopulationStandardDeviation" class="BNExec">&sigma;</button>
                        <button data-cmd="StandardDeviation" class="BNExec">SD</button>
                        <button data-cmd="Variance" class="BNExec">S2</button>
                        <button data-cmd="StandardError" class="BNExec">SE</button>
                        <button data-cmd="Mid" class="BNExec">Mid</button>
                        <button data-cmd="MeanDeviation" class="BNExec">M.D.</button>
                        <button data-cmd="Mode" class="BNExec">Mode</button>
                        <button data-cmd="Range" class="BNExec">Range</button>
                        <button data-cmd="MidRange" class="BNExec">MidRange</button>
                        <button data-cmd="Z" data-input="x:number" data-spreadsheetarrayindex="1" class="BNExecMultiple">Z</button>
                        <button data-cmd="ZTable" data-input="z:number" class="BNExecMultiple">Z-Table</button>
                        <button data-cmd="ZTableInvert" data-input="p:number" class="BNExecMultiple">Z-Table-Invert</button>
                        <button   data-cmd="Quantile" data-input="q:number" data-spreadsheetarrayindex="1" class="BNExecMultiple">Quantile</button>
                    </div>
                    <div style="background-color: burlywood;border-style: solid;border-width: thin;margin-top: 1px;">
                        <img id="OpenDialogBellChart" class="BNBolder"  src="../../../../../../img/statistics/bellchart.png" />
                        <img data-cmd="DrawPieChart" class="BNExeCellChart" src="../../../../../../img/statistics/piechart.png" />
                        <img data-cmd="DrawRingChart" class="BNExeCellChart" src="../../../../../../img/statistics/donutchart.png" />
                        <img data-cmd="DrawBarChart" class="BNExeCellChart" src="../../../../../../img/statistics/barchart.png" />
                        <img data-cmd="DrawLineChart" class="BNExeCellChart" src="../../../../../../img/statistics/linechart.png" />
                        <img data-cmd="DrawDotChart" class="BNExeCellChart" src="../../../../../../img/statistics/dotchart.png" />
                    </div>
                    <div id="SpreadSheet" style="overflow:auto;width: 100%; height: 100%;position: relative;">

                    </div>
                    <div  style="border-style: solid;border-width: thin;">        
                        <label>Chart:</label>
                        <canvas id="ImageOutput" width="800" height="600" style="width: 100%;border-style: solid;border-width: thin; "></canvas>
                    </div>

                </main>
                <aside>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Statistics</div>
                        <a class="MenuLink" href="#">Basic</a>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Output</div>
                        <div style="text-align: right;">
                            <a id="BNClearOutput" class="MenuLink"  href="#">Clear</a>
                        </div>

                        <textarea id="TXTOutput" style=" resize: vertical;width: 100%;box-sizing: border-box;min-height: 200px;"></textarea>
                    </div>


                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Event</div>
                        <?php
                        foreach ($event->GetComingEvent(Event_Database::Access_Member) as $value) {
                            echo '<div>';
                            printf('<a class="MenuLink" href="../../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <?php
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Aside)) {
                            echo ' <div class="BorderBlock" style="margin-top: 1px;" >';
                            printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                            echo $value->Execute(Module_SDK_Basic::Layout_Aside);
                            echo '</div>';
                        }
                    }
                    ?>
                </aside>

            </div>

            <table id="BellChartDialog" style="display: none;width: 98%;">
                <tr>
                    <td>Select:</td>
                    <td><select id="BellChartSelect"  style="width: 100%;box-sizing: border-box;">
                            <option value="1">Above</option>
                            <option value="2">Below</option>
                            <option value="3">Between</option>
                            <option value="4">Outside</option>
                        </select></td>
                </tr>
                <tr>
                    <td>Confidence Level-1:</td>
                    <td><input id="BellChartCL1" type="number" value="95" min="-100" max="100"/></td>
                </tr>
                <tr>
                    <td>Confidence Level-2:</td>
                    <td><input id="BellChartCL2" type="number" value="95" min="-100" max="100"/></td>
                </tr>
            </table>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../../Auth/Login.php");
    session_destroy();
}
