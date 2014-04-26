function setUpdateAction() {
document.frmUser.action = "edit_user.php";
document.frmUser.submit();
}
function setCreateAction() {
document.frmUser.action = "acopiar-z.php";
document.frmUser.submit();
}
function addAllToCartAction() {
document.add_all.action = "add_all_to_cart.php";
document.add_all.submit();
}
function setDeleteAction() {
if(confirm("Are you sure want to delete these rows?")) {
document.frmUser.action = "delete_user.php";
document.frmUser.submit();
}
}
