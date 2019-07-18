10个超棒jQuery表单操作代码片段
==============================

这不是我自己写的，我也不知道是谁写的，我是从网上的word中找到的。不管怎么样，谢谢作者。
-------------------------------------------------------------------------------------

### 代码片段1: 在表单中禁用“回车键”  

```
$("#form").keypress(function(e) {
    if (e.which == 13) { 
        return false; 
    } 
});
```

### 代码片段2: 清除所有的表单数据

```
function clearForm(form) {  
  // iterate over all of the inputs for the form
  // element that was passed in
  $(':input', form).each(function() {
    var type = this.type;
    var tag = this.tagName.toLowerCase(); // normalize case
    // it's ok to reset the value attr of text inputs,
    // password inputs, and textareas
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = "";
    // checkboxes and radios need to have their checked state cleared
    // but should *not* have their 'value' changed
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    // select elements need to have their 'selectedIndex' property set to -1
    // (this works for both single and multiple select elements)
    else if (tag == 'select')
      this.selectedIndex = -1;
  });
};
```

### 代码片段3: 将表单中的按钮禁用

禁用按钮：
```
$("#somebutton").attr("disabled", true);
```

启动按钮：
```
$("#submit-button").removeAttr("disabled");
```
可能大家往往会使用.attr(‘disabled’,false);，不过这是不正确的调用。

### 代码片段4: 输入内容后启用提交按钮

```
$('#username').keyup(function() {
    $('#submit').attr('disabled', !$('#username').val()); 
});
```

### 代码片段5: 禁止多次提交表单

```
$(document).ready(function() {
  $('form').submit(function() {
    if(typeof jQuery.data(this, "disabledOnSubmit") == 'undefined') {
      jQuery.data(this, "disabledOnSubmit", { submited: true });
      $('input[type=submit], input[type=button]', this).each(function() {
        $(this).attr("disabled", "disabled");
      });
      return true;
    }
    else
    {
      return false;
    }
  });
});
```

### 代码片段6: 高亮显示目前聚焦的输入框标示

```
$("form :input").focus(function() {
  $("label[for='" + this.id + "']").addClass("labelfocus");
}).blur(function() {
  $("label").removeClass("labelfocus");
});

```
### 代码片段7: 动态方式添加表单元素

```
//change event on password1 field to prompt new input
$('#password1').change(function() {
        //dynamically create new input and insert after password1
        $("#password1").append("<input type='text' name='password2' id='password2' />");
});

```
### 代码片段8: 自动将数据导入selectbox中

```
$(function(){
  $("select#ctlJob").change(function(){
    $.getJSON("/select.php",{id: $(this).val(), ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
      $("select#ctlPerson").html(options);
    })
  })
})
```

### 代码片段9: 判断一个复选框是否被选中

```
if($('#checkBox').attr('checked')){}
```

### 代码片段10: 使用代码来提交表单
```
$("#myform").submit();
```


^^ 都很简单^^

- learn git。。。
- learn markdoen 。。。