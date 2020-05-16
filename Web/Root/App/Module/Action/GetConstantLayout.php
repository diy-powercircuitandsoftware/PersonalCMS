<?php
  
include_once '../../../../../Class/Core/Module/Database.php';
$refl = new ReflectionClass('Module_Database');
echo json_encode($refl->getConstants());