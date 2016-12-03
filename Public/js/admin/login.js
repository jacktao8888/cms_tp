var login = {
    check : function(){
        var username = $('input[name="username"]').val();
        var password = $('input[name="password"]').val();

        
        if(!username){
            alert("用户名不能为空");
        }
        if(!password){
            alert("密码不能为空");
        }
        
        var url="/index.php?m=Admin&c=Login&a=check";
        var data={"username":username,"password":password};
        //异步请求$.postc
        $.post(url,data,function(result){
            if(result.status == '0'){
                return dialog.error(result.message);
            }
            if(result.status == '1'){
                return dialog.success(result.message,'index.php?m=Admin&c=Index');
            }
        },"JSON");
    }
}