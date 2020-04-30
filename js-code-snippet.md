### JavaScript && jQuery Snippet

``` jQuery
    $(document).on("change", "select[flag='FirstId'],select[flag='SecondId'],select[flag='ThirdId'],select[flag='FourthId']", function () {
        var selectedModule = $(":selected", $(this));	// 获取当前选择的 option
        var selectedModuleVal = selectedModule.attr("value");
        var selectedModuleFlag =  $(this).attr("flag");		// 获取 flag 属性
        var appendFlag = selectedModule.parent().parent().next().attr("flag");
        if(selectedModuleFlag=="FirstId"){
            selectedModuleFlag = "SecondId";
        }else if(selectedModuleFlag=="SecondId"){
            selectedModuleFlag = "ThirdId";
        }else if(selectedModuleFlag=="ThirdId"){
           selectedModuleFlag = "FourthId";
        }
        //step-01: ajax get first module data
		...
    });
```