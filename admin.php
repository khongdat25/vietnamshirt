<?php
include_once './Controllers/AdminController.php';
// include_once "./Views/admin/layout_header.php";

if (isset($_GET['ctrl']) && isset($_GET['act'])) {
    include_once 'Controllers/' . ucfirst($_GET['ctrl']) . 'Controller.php';
    $ctrl = new (ucfirst($_GET['ctrl']) . 'Controller')();
    $act = $_GET['act'];
    // Thay thế 2 dòng lỗi trên bằng đoạn này:
if (!empty($_GET['id'])) {
    $ctrl->$act($_GET['id']);
} else {
    $ctrl->$act();
}
} 
else {
    include_once 'Controllers/AdminController.php';
    $ctrl = new AdminController();
    $ctrl->pageAdmin(); 
}


// include_once "./Views/admin/layout_footer.php";
?>