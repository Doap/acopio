function setUpdateAction() {
document.frmUser.action = "/manifiesto/edit_user.php";
document.frmUser.submit();
}
function setCreateAction() {
document.frmUser.action = "/manifiesto/create_manifiesto.php";
document.frmUser.submit();
}
function setDeleteAction() {
if(confirm("Are you sure want to delete these rows?")) {
document.frmUser.action = "/manifiesto/delete_user.php";
document.frmUser.submit();
}
}
