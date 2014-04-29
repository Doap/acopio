function setUpdateAction() {
document.frmUser.action = "/pitaya/edit_user.php";
document.frmUser.submit();
}
function setCreateAction() {
document.frmUser.action = "/pitaya/acopiar-z.php";
document.frmUser.submit();
}
function addAllToCartAction() {
document.add_all.action = "/pitaya/add_all_to_cart.php";
document.add_all.submit();
}
function setDeleteAction() {
if(confirm("Are you sure want to delete these rows?")) {
document.frmUser.action = "/pitaya/delete_user.php";
document.frmUser.submit();
}
}
