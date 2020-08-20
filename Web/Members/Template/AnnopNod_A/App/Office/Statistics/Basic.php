<?php
session_start();
include_once '../../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../../Class/DB/Com/Events/Viewer.php';
include_once '../../../../../../Class/DB/Com/Module/LoadModule.php';
include_once '../../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../../Class/DB/Com/User/Permission.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$Event = new Com_Events_Viewer($DBConfig);
$Module = new Com_Module_LoadModule($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
$Permission = new Com_User_Permission($DBConfig);
$DBConfig->Open();
if ($SC->Online() && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Untitled Document</title>
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
            <link rel="stylesheet" href="../../css/Page.css">
            <?php
            foreach ($UModule->LoadModule($_SESSION["UserID"], Com_User_LoadModule::Layout_Head) as $value) {
                try {
                    include_once '../../../../../../Class/DB/UserModule/' . $value["filename"];
                    $mod = new $value["classname"]($UModule);
                    $mod->LoadConfig($value["config"]);
                    echo $mod->Execute();
                } catch (Exception $ex) {
                    
                }
            }
            ?>
            <script src="../../../../../js/dom/SuperDialog.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/statistical/SpreadSheet.js"></script>
            <script src="../../../../../js/statistical/Statistical.js"></script>
            <script src="../../../../../js/statistical/BellChart.js"></script>
            <script src="../../../../../js/statistical/Chart.js"></script>
            <script>
                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ExecChart = new Chart(document.getElementById("ImageOutput"));
                    var sd = new SuperDialog();
                    var stat = new Statistical();
                    var ssh = document.getElementById("SpreadSheet").appendChild(new SpreadSheet(12, 24));
                    var bc = new BellChart(document.getElementById("ImageOutput"));

                    //ssh.AddRow(1);
                    //http://ie.eng.cmu.ac.th/IE2014/elearnings/2015_01/183/Minitab.pdf
                    //http://www.stvc.ac.th/elearning/stat/csu5.html
                    if (ss.URLParam()["path"] !== undefined) {
                        var dpw = sd.PleaseWait().ZIndex(999);
                        ss.Post("../../../../Api/Ajax/CSV/GetCSVData.php", {"path": ss.URLParam()["path"]}, function (data) {
                            data = JSON.parse(data);
                            ssh.ImportFromCSV(data["csvdata"]);
                            document.title = data["name"];
                            dpw.Close();
                        });
                    }


                    ss.S("#BNClear").Click(function () {
                        if (ss.URLParam()["path"] !== undefined) {
                            sd.Confirm("Do You Clear Data", function () {
                                ssh.Reset();
                            }).ZIndex(999);
                        } else {
                            ssh.Reset();
                        }

                    });
                    ss.S("#BNClearOutput").Click(function () {
                        ss.S("#TXTOutput").Html("");
                    });

                    ss.S(".BNExec").Click(function () {
                        ss.S("#TXTOutput").Html(ss.S("#TXTOutput").Html() + "<br>" + this.getAttribute("data-cmd") + "=" + stat[this.getAttribute("data-cmd")](ssh.GetAllNumber()));
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
                            ss.S("#TXTOutput").Html(ss.S("#TXTOutput").Html() + "<br>" + cmd + "=" + stat[cmd].apply(stat, argss));
                            return true;
                        }).ZIndex(999).Title(this.getAttribute("data-cmd"));
                        t.args = [];
                        var sp = this.getAttribute("data-input").split(",");
                        for (var i = 0; i < sp.length; i++) {
                            var spclock = sp[i].split(":");
                            if (spclock[1] == "number") {
                                t.args.push(t.AddTableDom(spclock[0] + ":", '<input type="number"  />'));
                            }
                        }

                    });
                    ss.S(".BNExeCellChart").Click(function () {

                        var cmd = this.getAttribute("data-cmd");
                        var t = sd.TableLayout(function () {
                            //   console.log(ssh.GetNumberAtCell(t.cell1.value));
                            if (t.option.value = "0") {
                                var dat = ssh.GetAllNumber();
                                for (var i = 0; i < dat.length; i++) {
                                    ExecChart.SetData(i + 1, dat[i]);
                                }

                            }
                            ExecChart[cmd]();
                            return true;

                        }).ZIndex(999).Title(this.getAttribute("data-cmd"));
                        t.option = t.AddTableDom('Plot', '<select style="width: 100%;box-sizing: border-box;"><option value="0">All Data</option><option value="1">Average By Cell</option><option value="2">Start Cell AS Label</option></select>');
                        t.cell1 = t.AddTableDom('Start Cell Index', '<input style="width: 100%;box-sizing: border-box;" type="number" min="1" value="" />');
                        t.cell2 = t.AddTableDom('Stop Cell Index', '<input style="width: 100%;box-sizing: border-box;" type="number" min="1" value="" />');

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
                        sd.Import("#BellChartDialog", function () {
                            var sv = ss.S("#BellChartSelect").Val();
                            var za21 = stat.ConfidenceLevelTOZA2(parseFloat(ss.S("#BellChartCL1").Val()) / 100);
                            var za22 = stat.ConfidenceLevelTOZA2(parseFloat(ss.S("#BellChartCL2").Val()) / 100);
                            var arrdata = ssh.GetAllNumber();
                            var m = stat.Average(arrdata);
                            var sd = stat.StandardDeviation(arrdata);
                            var txtout = ss.S("#TXTOutput").Html() + "<br>";
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
                            ss.S("#TXTOutput").Html(txtout);
                        }).ZIndex(999).Title("Bell Curve");
                    });




                });
            </script>
        </head>
        <body >

            <div id="Header" style="position: absolute;" >
                <div style="width: 50%;">
                    <a href="../../index.php">
                        <img  src="../../../../../../File/Resource/Logo.png"/>
                    </a>
                </div>
                <div  style="width: 50%;text-align: right;">
                    <a href="../../index.php">MainPage</a>
                    <?php
                    $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                    printf('<img  src="../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s" />', $Dat["userid"]);
                    echo '<span>' . $Dat["alias"] . '</span>';
                    ?>
                    <a href="../../Config/Config.php">Config</a>

                    <a  href="../../../../Session/Action/Logout.php">Logout</a>
                </div>
            </div>
            <div class="Container">
                <div class="Nav">
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Audio</span>
                        <ul>
                            <li><a href="../../Audio/Player.php">Player</a></li>
                            <li><a href="../../Audio/PlayList.php">PlayList</a></li>

                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Blog</span>
                        <ul>
                            <li><a href="../../Blog/Manage.php">Manage</a></li>
                            <li><a href="../../Blog/View.php">View</a></li>
                        </ul>
                    </div>

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Event</span>
                        <ul>
                            <li><a href="../../Event/Manage.php">Manage</a></li>
                            <li><a href="../../Event/View.php">View</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Files</span>
                        <ul>
                            <li><a href="../../Files/Manager.php">Manager</a></li>
                            <li><a href="../../Files/Temp.php">Temp</a></li>
                            <li><a href="../../Files/Trash.php">Trash</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Office</span>
                        <ul>
                            <li><a href="../FinFin/MainPage.php">FinFin</a></li>
                            <li><a href="../FlowFlow/MainPage.php">FlowFlow</a></li>
                            <li><a href="../Image/MainPage.php">Image</a></li>
                            <li><a href="../PointPoint/MainPage.php">PointPoint</a></li>
                            <li style="font-weight: bold;">Statistics</li>
                            <li><a href="../WordWord/MainPage.php">WordWord</a></li>
                            <li><a href="../WYSIWYG/NewDoc.php">WYSIWYG</a></li>
                            <li><a href="../XCell/MainPage.php">XCell</a></li>
                            <li><a href="../XCess/MainPage.php">XCess</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Photo</span>
                        <ul>
                            <li><a href="../../Photo/ImageSlider.php">ImageSlider</a></li>
                            <li><a href="../../Photo/PlayList.php">PlayList</a></li>
                        </ul>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">Share</span>
                        <ul>
                            <li><a href="../../Share/BlogViewer.php">Blog</a></li>
                            <li><a href="../../Share/EventViewer.php">Event</a></li>
                        </ul>
                    </div>
                    <?php
                    $Dat = array_merge($Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Members), $Module->LoadModule(Com_Module_LoadModule::Layout_Nav, Config_DB_Config::Access_Mode_Public));
                    foreach ($Dat as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModuleID($value["id"]);
                            $mod->SetModulePage("../../Module/Page.php");
                            $mod->SetUserID($_SESSION["UserID"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>
                </div>
                <div  class="Section" style="box-sizing: border-box;">
                    <div style="background-color: burlywood;border-style: solid;border-width: thin;">

                        <img id="BNOpen"  style="border-style: outset;" src="../img/wysiwyg/open.gif" width="22" height="22">
                        <?php
                        if (isset($_GET["path"]) && $Permission->Writable($_SESSION["UserID"])) {
                            echo '<img id="BNSave"  style="border-style: outset;" src="../img/wysiwyg/save.gif" width="22" height="22">';
                        }
                        ?>
                        <img id="BNClear" style="border-style: outset;" src="../img/wysiwyg/removeformat.gif" width="22" height="22">

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
                        <img id="OpenDialogBellChart" class="BNBolder"  src="../img/statistics/bellchart.png" />
                        <img data-cmd="DrawPieChart" class="BNExeCellChart" src="../img/statistics/piechart.png" />
                        <img data-cmd="DrawRingChart" class="BNExeCellChart" src="../img/statistics/donutchart.png" />
                        <img data-cmd="DrawBarChart" class="BNExeCellChart" src="../img/statistics/barchart.png" />
                        <img data-cmd="DrawLineChart" class="BNExeCellChart" src="../img/statistics/linechart.png" />
                        <img data-cmd="DrawDotChart" class="BNExeCellChart" src="../img/statistics/dotchart.png" />
                    </div>
                    <div id="SpreadSheet" style="overflow-y: auto;">

                    </div>
                    <div  style="border-style: solid;border-width: thin;">
                        <div style="display: none;display: flex;flex-direction: row;">
                            <div style="width: 50%;"><label>Output:</label></div>
                            <div  style="width: 50%;text-align: right;"><a id="BNClearOutput" href="#">Clear</a></div>
                        </div>


                        <div id="TXTOutput" style=" min-height: 300px;background-color: white;border-style: solid;border-width: thin;"></div>
                        <label>Chart:</label>
                        <canvas id="ImageOutput" width="800" height="600" style="width: 100%;border-style: solid;border-width: thin; "></canvas>


                    </div>

                </div>
                <div class="Aside"  >

                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Statistics</label>
                        <a href="#">Basic</a>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">My Event</label>
                        <?php
                        foreach ($Event->GetCurrentMyEvent($_SESSION["UserID"]) as $value) {
                            echo '<div>';
                            printf('<a href="../../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <label class="Title">Other Event</label>
                        <?php
                        $Dat = array_merge($Event->GetCurrentEventNotUserID(Config_DB_Config::Access_Mode_Members, $_SESSION["UserID"]), $Event->GetCurrentEventNotUserID(Config_DB_Config::Access_Mode_Public, $_SESSION["UserID"]));
                        foreach ($Dat as $value) {
                            echo '<div  >';
                            printf('<a href="../../Share/EventViewer.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <?php
                    $Dat = array_merge($Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Members), $Module->LoadModule(Com_Module_LoadModule::Layout_Aside, Config_DB_Config::Access_Mode_Public));
                    foreach ($Dat as $value) {
                        try {
                            echo ' <div class="BorderBlock" style="margin-top: 3px;" >';
                            include_once '../../../../../../Class/DB/Module/' . $value["filename"];
                            $mod = new $value["classname"]($Module);
                            printf('<label class="Title">%s</label>', $mod->GetTitle());
                            $mod->SetModuleID($value["id"]);
                            $mod->SetModulePage("../../Module/Page.php");
                            $mod->SetUserID($_SESSION["UserID"]);
                            echo $mod->Execute();
                            echo '</div>';
                        } catch (Exception $ex) {
                            
                        }
                    }
                    ?>
                </div>
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
    header("location: ../../../../Session/AuthUserID.php");
    session_destroy();
}
