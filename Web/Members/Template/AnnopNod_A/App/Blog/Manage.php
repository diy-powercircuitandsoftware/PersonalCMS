<?php
session_start();
include_once '../../../../../../Class/Core/Config/Config.php';
include_once '../../../../../../Class/Core/UI/NAV.php';
include_once '../../../../../../Class/Core/Module/Database.php';
include_once '../../../../../../Class/Com/Event/Database.php';
include_once '../../../../../../Class/Com/Event/Reader.php';
include_once '../../../../../../Class/Com/Blog/Database.php';
include_once '../../../../../../Class/Com/Category/Database.php';
include_once '../../../../../../Class/SDK/Module/Basic.php';
include_once '../../../../Auth/Action/VerifySession.php';

$config = new Config();
$uinav = new UINAV();
$module = new Module_Database($config);
$category = new Category_Database($config);
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
            <title><?php echo basename(__FILE__, ".php"); ?></title>
            <link rel="stylesheet" type="text/css" href="../../../../../css/HolyGrail.css">
            <link rel="stylesheet" type="text/css" href="../../../../../css/PersonalCMS.css">

            <?php
            foreach ($modlist as $value) {
                echo $value->Execute(Module_SDK_Basic::Layout_Head);
            }
            ?>
            <script src="../../../../../js/io/Ajax.js"></script>
            <script src="../../../../../js/dom/SSQueryFW.js"></script>
            <script src="../../../../../js/dom/SuperDialog.js"></script>          
            <script src="../../../../../js/dom/TableTools.js"></script>
            <script src="../../../../../js/dom/FilesList.js"></script>
            <script src="../../../../../js/dom/SpanList.js"></script>
            <script>

                var ss = new SSQueryFW();
                ss.DocumentReady(function () {
                    var ajax = new Ajax();
                    var sd = new SuperDialog();
                    var tabletool = new TableTools();
                    var EKeyword = new SpanList("#EKeyword");
                    var FL = new FilesList("#FilesList");
                    var ajaxsb = new AjaxScrollBar("../../../../Api/Ajax/Blog/GetBlogListForEdit.php", {"id": 0});
                    FL.Multiple(false);
                    var lastid = 0;
                    tabletool.Import(document.getElementById("TableOutput"));
                    ajaxsb.AddScrollEvent(function (data) {
                        try {
                            data = JSON.parse(data);
                            for (var i in data) {
                                tabletool.InsertRow();
                                tabletool.InsertCellLastRow('<div style="text-align: center;"><input type="checkbox" class="SelectID" value="' + data[i]["id"] + '" /></div>');
                                tabletool.InsertCellLastRow(data[i]["title"]);
                                tabletool.InsertCellLastRow(data[i]["htmlfilepath"]);
                                tabletool.InsertCellLastRow(data[i]["enable"]);
                                tabletool.InsertCellLastRow(data[i]["public"]);
                                tabletool.InsertCellLastRow('<button class="BNEdit" data-value="' + data[i]["id"] + '">Edit</button>');
                                lastid = Math.max(lastid, data[i]["id"]);
                            }
                            ajaxsb.Param("id", lastid);
                        } catch (e) {
                            sd.Alert(data);
                        }
                    });
                    FL.OpenDir(function (v) {
                        ajax.Post("../../../../Api/Ajax/Files/GetFilesListByExtension.php", {"Path": v, "Ext": "BlogZip"}, function (data) {
                            FL.Clear();
                            data = JSON.parse(data);
                            for (var i in data) {
                                if (data[i]["type"] == "DIR") {
                                    FL.AddDir(data[i]["name"], data[i]["fullpath"], data[i]["modified"]);
                                } else if (data[i]["type"] == "FILE") {
                                    FL.AddFile(data[i]["name"], data[i]["fullpath"], data[i]["size"], data[i]["modified"]);
                                }
                            }
                            ss.S("#CHDIRList").Html(decodeURIComponent(v));
                        });
                    });

                    EKeyword.Input(function (v) {
                        var ref = this;
                        ajax.Post("../../../../Api/Ajax/Category/SearchKeyword.php", {"Keyword": v}, function (data) {
                            ref.RemoveList();
                            data = JSON.parse(data);
                            for (var i = 0; i < data.length; i++) {
                                ref.AddList(data[i]["id"], data[i]["name"]);
                            }
                        });
                    });

                    tabletool.AddEventListener("click", function (e) {
                        if (e.target.getAttribute("class") == "SelectID") {
                            if (!e.target.checked) {
                                ss.S("#CBoxSelectAll").Val(false);
                            }
                        } else if (e.target.getAttribute("class") == "BNEdit") {
                            // {}
                            ajax.Post("../../../../Api/Ajax/Blog/GetBlogDataForEdit.php", {"id": e.target.getAttribute("data-value")}, function (edata) {

                                edata = JSON.parse(edata);
                                var category = edata["category"];
                                var keyword = [];
                                FL.OpenDir("/");
                                ss.S(".AjaxSendEdit").ValByName(edata);
                                for (var i in category) {
                                    keyword.push(category[i]["keywordid"]);
                                }
                                ajax.Post("../../../../Api/Ajax/Category/GetKeywordDataByID.php", {"id": keyword}, function (kdata) {
                                    kdata = JSON.parse(kdata);
                                  EKeyword.Empty();
                                    for (var i in kdata) {
                                        EKeyword.AddItem(kdata[i]["id"],kdata[i]["name"]);
                                    }
                                });
                                

                                sd.ImportOkCancel("Edit", "#Dialog", function () {
                                    var senddata = ss.S(".AjaxSendEdit").ValByName();
                                    senddata["htmlfilepath"] = FL.GetSelectFiles(0);
                                    senddata["keyword"] = EKeyword.GetItems();

                                    ss.Post("../../../Api/Ajax/BlogManager/EditBlog.php", senddata, function (d) {
                                       ajaxsb.LoadAjax();
                                    });
                                }).ZIndex(999).Title("Edit");

                            });

                        }
                    });

                    /*       var wsl = ss.WindowScrollLoad();
                     wsl.URL = "../../../Api/Ajax/BlogManager/GetBlogList.php";
                     wsl.Param["StartID"] = 0;
                     wsl.Done = (function (data) {
                     data = JSON.parse(data);
                     for (var i = 0; i < data.length; i++) {
                     
                     tabletool.InsertRow();
                     tabletool.InsertCellLastRow('<input type="checkbox" class="SelectID" value="' + data[i]["id"] + '" />');
                     tabletool.InsertCellLastRow(data[i]["title"]);
                     tabletool.InsertCellLastRow(data[i]["categoryid"]);
                     tabletool.InsertCellLastRow('<a href="#" class="fullpathfile" data-id="' + data[i]["htmlfilepath"] + '" >' + data[i]["filename"] + '</a>');
                     
                     tabletool.InsertCellLastRow('<button class="BNEdit" data-id="' + data[i]["id"] + '">Edit</button>');
                     wsl.Param["StartID"] = Math.max(parseInt(data[i]["id"]), wsl.Param["StartID"]);
                     }
                     wsl.Lock = false;
                     });*/



                    ss.S("#BNAdd").Click(function () {

                        ss.S(".AjaxSendEdit").Val("");
                        ss.S("#EFilePath").Html("");
                        EKeyword.Empty();
                        FL.OpenDir("/");
                        sd.ImportOkCancel("Add", "#Dialog", function () {

                            var senddata = ss.S(".AjaxSendEdit").ValByName();
                            senddata["htmlfilepath"] = FL.GetSelectFiles(0);
                            senddata["keyword"] = EKeyword.GetItems();

                            ajax.Post("../../../../Api/Ajax/Blog/AddBlog.php", senddata, function (d) {
                                tabletool.DeleteRowAfter(0);

                            });
                        }).ZIndex(999);

                    });
                    ss.S("#BNRemove").Click(function () {
                        sd.Confirm("Do You Delect It", function () {
                            var v = ss.S(".SelectID").Val();
                            ajax.Post("../../../../Api/Ajax/Blog/DeleteBlog.php", {"ID": v}, function () {
                                /* tabletool.DeleteRowAfter(0);
                                 wsl.Param["StartID"] = 0;
                                 wsl.Lock = false;
                                 wsl.LoadData();*/
                            });
                        }).ZIndex(999);
                    });
                    ss.S("#CBoxSelectAll").Click(function () {
                        ss.S(".SelectID").Val(this.checked);
                    });

                });
            </script>
        </head>
        <body  class="HolyGrail">

            <header class="Header">
                <div style="width: 50%;"></div>
                <div style="width: 50%;text-align: right;">
                    <?php
                    printf('<img src="../../../../Api/Action/Profile/GetUserIcon.php?id=%s"/>', $_SESSION["User"]["id"]);
                    printf('<span style="font-weight: bold;cursor: default;">%s</span>', $_SESSION["User"]["alias"]);
                    ?>       
                    <a class="MenuLink" style="display: inline;" href="../../../../Auth/Action/Logout.php">LogOut</a>
                </div>
            </header>
            <div class="HolyGrail-body">
                <nav>
                    <?php
                    foreach ($uinav->FindAllMenuFile("../../App") as $key => $valueA) {
                        echo '<div class="BorderBlock">';
                        printf(' <div class="TitleCenter">%s</div>', $key);
                        foreach ($valueA as $valueB) {
                            printf('  <a class="MenuLink" href="%s">%s</a>', $valueB["path"], $valueB["name"]);
                        }
                        echo '</div>';
                    }
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Nav)) {
                            echo ' <div class="BorderBlock" style="margin-top: ๅpx;" >';
                            printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                            echo $value->Execute(Module_SDK_Basic::Layout_Nav);
                            echo '</div>';
                        }
                    }
                    ?>     
                </nav>
                <main>

                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        echo '<table style="width: 100%;text-align: center;" id="TableOutput">
                        <tr>
                            <th>Select</th>
                            <th>Title</th>
                             <th>FileName</th>
                             <th>Enable</th>
                             <th>Public</th>
                            <th>Edit</th>
                        </tr>
                    </table>';
                    }
                    ?>
                </main>  
                <aside>
                    <?php
                    if ($_SESSION["User"]["writable"] == 1) {
                        echo ' <div class="BorderBlock">
                            <div class="TitleCenter">Manage</div>
                             <span style="display: block;"><input type="checkbox" id="CBoxSelectAll"  /> Select All</span>
                              <a id="BNAdd" class="MenuLink" href="#">Add</a>
                               <a id="BNRemove" class="MenuLink" href="#">Remove</a>
                             
                        </div>';
                    }
                    ?>
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <div class="TitleCenter">Event</div>
                        <?php
                        foreach ($event->GetComingEvent(Event_Database::Access_Member) as $value) {
                            echo '<div>';
                            printf('<a href="../Event/View.php?id=%s"><span style="font-weight: bold;">%s</span>', $value["id"], $value["name"]);
                            printf('<div style="color: black;" >%s</div></a>', $value["description"]);
                            echo '</div><hr>';
                        }
                        ?>
                    </div>
                    <?php
                    foreach ($modlist as $value) {
                        if ($value->SupportLayout(Module_SDK_Basic::Layout_Aside)) {
                            echo ' <div class="BorderBlock" style="margin-top: ๅpx;" >';
                            printf('<div class="TitleCenter">%s</div>', $value->GetTitle());
                            echo $value->Execute(Module_SDK_Basic::Layout_Aside);
                            echo '</div>';
                        }
                    }
                    ?>
                </aside>
            </div>
            <footer>
                <span style="font-weight: bold;display: block;">
                    <?php
                    echo "&COPY;" . date("Y") . " " . $config->GetName();
                    ?>
                </span>  
            </footer>
            <div id="Dialog" style="display: none;">
                <table style="width: 98%;">
                    <tr>
                        <td>Title:</td>
                        <td><input type="text" name="title" class="AjaxSendEdit"  style="width: 99%;"></td>
                    </tr>
                    <tr>
                        <td>Category:</td>
                        <td>
                            <select class="AjaxSendEdit" name="categoryid"  style="width: 100%;">
                                <option value="">==Select==</option>
                                <option value="0">None</option>
                                <?php
                                foreach ($category->GetAllCategory() as $value) {
                                    printf('<option value="%s">%s</option>', $value["id"], $value["name"]);
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Keyword</td>
                        <td id="EKeyword"></td>
                    </tr>
                    <tr>
                        <td>Access:</td>
                        <td>
                            <select class="AjaxSendEdit" name="public"  style="width: 100%;">
                                <?php
                                printf('<option value="%s">%s</option>', Blog_Database::Access_Member, "Member");
                                printf('<option value="%s">%s</option>', Blog_Database::Access_Public, "Public");
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>enable:</td>
                        <td>
                            <select class="AjaxSendEdit" name="enable"  style="width: 100%;">
                                <option value="1">yes</option>
                                <option value="0">no</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td>
                            <textarea style="min-width:100%;resize: vertical; " class="AjaxSendEdit" name="description" rows="4" cols="20"></textarea>
                        </td>
                    </tr>
                </table>
                <div>
                    <div id="FilesList">
                        <div id="CHDIRList"></div>
                    </div>
                </div>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../Auth/Login.php");
    session_destroy();
}
