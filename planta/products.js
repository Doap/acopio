function setUpdateAction() {
document.frmUser.action = "/planta/edit_user.php";
document.frmUser.submit();
}
function setCreateAction() {
document.frmUser.action = "/planta/acopiar-z.php";
document.frmUser.submit();
}
function addAllToCartAction() {
document.add_all.action = "/planta/add_all_to_cart.php";
document.add_all.submit();
}
function setDeleteAction() {
if(confirm("Are you sure want to delete these rows?")) {
document.frmUser.action = "/planta/delete_user.php";
document.frmUser.submit();
}
}
