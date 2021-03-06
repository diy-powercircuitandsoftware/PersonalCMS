<?php
session_start();
include_once '../../../../../../Class/DB/Config/DB/Config.php';
include_once '../../../../../../Class/DB/Config/DB/Software.php';
include_once '../../../../../../Class/DB/Com/User/SessionManager.php';
include_once '../../../../../../Class/DB/Com/User/Profile.php';
include_once '../../../../../../Class/DB/Com/User/LoadModule.php';
include_once '../../../../../../Class/DB/Com/User/ConfigModule.php';
$DBConfig = new Config_DB_Config();
$SC = new Config_DB_Software($DBConfig);
$Sess = new Com_User_SessionManager($DBConfig);
$User = new Com_User_Profile($DBConfig);
$UModule = new Com_User_LoadModule($DBConfig);
$DBConfig->Open();
if ($SC->Online() && isset($_SESSION["UserID"]) && $Sess->Registered(session_id())) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php echo $SC->GetName(); ?></title>
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
        </head>
        <body>
            <div id="Header">
                <div style="width: 50%;">
                    <img  src="../../../../../../File/Resource/Logo.png"/>
                </div>
                <div  style="width: 50%;text-align: right;">
                    <?php
                    $Dat = $User->GetBasicUserData($_SESSION["UserID"]);
                    printf('<img  src="../../../../Api/Action/Profile/Basic/GetUserIcon.php?id=%s" />', $Dat["userid"]);
                    echo '<span>' . $Dat["alias"] . '</span>';
                    ?>
                    <a href="../../index.php">MainPage</a>
                    <a href="../../../../Session/Action/Logout.php">Logout</a>
                    
                </div>
            </div>
            <div class="Container">
                <div class="Nav">
                    <div class="BorderBlock" style="margin-top: 1px;">
                        <span class="Title" style="display: block;">MyModule</span>
                        <ul>
                            <li><a href="Config.php">Config</a></li> 
                        </ul>
                    </div>
                </div>

                <div class="Section" >

                    <?php
                    if (isset($_GET["ModuleName"])) {
                        $fname = '../../../../../../Class/DB/UserModule/' . $_GET["ModuleName"];

                        if (is_file($fname)) {
                            include_once $fname;
                            $Classname = GetClassNameFromFile($fname);
                            $class = new $Classname();
                            echo "Description:" . $class->description;
                            echo $class->GetConfigForm("Action/Config.php",new Com_User_ConfigModule($DBConfig));
                        }
                    } else {
                        $sdir = scandir('../../../../../../Class/DB/UserModule/');
                        foreach ($sdir as $value) {
                            $ext = pathinfo($value, PATHINFO_EXTENSION);
                            if ($ext == "php") {
                                printf('<a href="Config.php?ModuleName=%s">%s</a>', $value, $value);
                            }
                        }
                    }
                    ?>
                </div>
                <div class="Aside">
                    <div class="BorderBlock">

                    </div>

                </div>
            </div>
        </body>
    </html>
    <?php
} else {
    header("location: ../../../../Session/AuthUserID.php");
    session_destroy();
}
