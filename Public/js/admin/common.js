/*
添加按钮操作
*/
$('#button-add').click(function(){
    var url = SCOPE.add_url;
    window.location.href = url;
});

/*
提交表单操作
*/
$("#singcms-button-submit").click(function(){
    var data = $("#singcms-form").serializeArray();
    postData = {};
    $(data).each(function(i){
        postData[this.name] = this.value;
    });
    

    $.post(SCOPE.save_url,postData,function(result){
       if(result.status == 1){
           //成功
           return dialog.success(result.message,SCOPE.jump_url);
       }else if(result.status == 0){
           //失败
           return dialog.error(result.message);
       }
    },'JSON');
});

/*
编辑按钮操作
*/
$('.singcms-table #singcms-edit').on('click',function(){
    var id = $(this).attr('attr-id');
    var url = SCOPE.edit_url + '&id=' + id;
    window.location.href = url;
});

/*
删除按钮操作
*/
$('.singcms-table #singcms-delete').on('click',function(){
    var id = $(this).attr('attr-id');
    var a = $(this).attr('attr-a');
    var message = $(this).attr('attr-message');
    var url = SCOPE.set_status_url;
    
    data = {};
    data['id'] = id;
    data['status'] =  -1;
    layer.open({
        type : 0,
        title: '是否提交?',
        content:'是否确定'+message,
        btn:['yes','no'],
        icon:3,
        scrollbar:true,
        closeBtn:2,
        yes:function(){
            todelete(url,data);  
        },
    });
})

function todelete(url,data){
    $.post(url,data,function(s){
        if(s.status == 1){
            return dialog.success(s.message,'');
        }else{
            return dialog.error(s.message);
        }
            
    },"JSON");
}

/*推送js相关*/
$("#singcms-push").click(function(){
    var id = $("#select-push").val();
    push = {};
    postData = {};
    $('input[name="pushcheck"]:checked').each(function(i){
        push[i]=$(this).val();
    });
    postData['push'] = push;
    postData['position_id'] = id;
    
    var url = SCOPE.push_url;
    $.post(url,postData,function(result){
        if(result.status == 1){
            return dialog.success(result.message,result['data']['jump_url']);
        }
        if(result.status == '0'){
            return dialog.error(result.message);
        }
    },"JSON");
});